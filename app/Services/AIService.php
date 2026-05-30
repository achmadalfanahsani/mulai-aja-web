<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $apiKey;

    protected $baseUrl;

    protected $model;

    public function __construct() {
        $this->apiKey = config('services.gemini.api_key');
        // Use a simpler base URL and append the specific action later
        $apiVersion = config('services.gemini.api_version', 'v1beta');
        $this->baseUrl = "https://generativelanguage.googleapis.com/{$apiVersion}";
        $this->model = config('services.gemini.model', 'gemini-1.5-flash-latest');
    }

    /**
     * Generate questions and options from raw text.
     */
    public function generateMultipleChoice($rawText)
    {
        if (! $this->apiKey) {
            throw new \Exception('Gemini API Key is not configured in .env file (GEMINI_API_KEY).');
        }

        $prompt = <<<PROMPT
Anda adalah asisten pembuat soal ujian. 
Tugas Anda adalah mengambil daftar pertanyaan mentah dan menghasilkan 5 pilihan jawaban (A, B, C, D, E) untuk setiap pertanyaan, serta menentukan jawaban yang benar.

Input:
Daftar pertanyaan dalam format bebas.

Output:
Harus berupa JSON valid dengan format array of objects:
[
  {
    "question_text": "Teks pertanyaan",
    "options": {
      "A": "Opsi A",
      "B": "Opsi B",
      "C": "Opsi C",
      "D": "Opsi D",
      "E": "Opsi E"
    },
    "correct_answer": "A",
    "explanation": "Penjelasan singkat (opsional)"
  }
]

Aturan:
1. Pastikan JSON valid.
2. Opsi harus ada 5 (A-E).
3. 'correct_answer' harus berupa salah satu huruf kapital: A, B, C, D, atau E.
4. Jika pertanyaan tidak jelas, cobalah yang terbaik atau buat soal yang masuk akal.

Pertanyaan:
$rawText
PROMPT;

        try {
            $url = "{$this->baseUrl}/models/{$this->model}:generateContent";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-goog-api-key' => $this->apiKey,
            ])->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                ],
            ]);

            if ($response->failed()) {
                $status = $response->status();
                $body = $response->body();
                Log::error("Gemini API Error [$status]: ".$body);

                if ($status === 404) {
                    throw new \Exception("Model '{$this->model}' tidak ditemukan atau endpoint salah (404). URL: $url. Silakan periksa koneksi atau model yang digunakan.");
                }

                throw new \Exception('AI Service Error: '.($response->json('error.message') ?? "Status $status"));
            }

            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (! $text) {
                Log::error('Gemini API Invalid Response Structure: '.json_encode($data));
                throw new \Exception('Struktur respon dari AI tidak valid.');
            }

            // Terkadang model membungkus dengan ```json ... ```
            $text = preg_replace('/^```json\s*|\s*```$/', '', trim($text));

            $decoded = json_decode($text, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Decode Error: '.json_last_error_msg().' | Text: '.$text);
                throw new \Exception('Gagal memproses format data dari AI.');
            }

            return $decoded;

        } catch (\Exception $e) {
            Log::error('AI Generation Exception: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate essay questions from raw text.
     */
    public function generateEssay($rawText)
    {
        if (! $this->apiKey) {
            throw new \Exception('Gemini API Key is not configured in .env file (GEMINI_API_KEY).');
        }

        $prompt = <<<PROMPT
Anda adalah asisten pembuat soal ujian. 
Tugas Anda adalah mengambil daftar pertanyaan mentah atau materi, dan menghasilkan daftar pertanyaan isian singkat (essay) beserta kunci jawabannya.

Input:
Daftar pertanyaan atau materi dalam format bebas.

Output:
Harus berupa JSON valid dengan format array of objects:
[
  {
    "question_text": "Teks pertanyaan",
    "correct_answer": "Kunci jawaban singkat",
    "explanation": "Penjelasan singkat atau kriteria penilaian (opsional)"
  }
]

Aturan:
1. Pastikan JSON valid.
2. 'correct_answer' harus berupa teks jawaban yang benar.
3. Jika pertanyaan tidak jelas, cobalah yang terbaik atau buat soal yang masuk akal.

Pertanyaan:
$rawText
PROMPT;

        try {
            $url = "{$this->baseUrl}/models/{$this->model}:generateContent";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-goog-api-key' => $this->apiKey,
            ])->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                ],
            ]);

            if ($response->failed()) {
                $status = $response->status();
                Log::error("Gemini API Error [$status]: ".$response->body());
                throw new \Exception('AI Service Error: '.($response->json('error.message') ?? "Status $status"));
            }

            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (! $text) {
                throw new \Exception('Struktur respon dari AI tidak valid.');
            }

            $text = preg_replace('/^```json\s*|\s*```$/', '', trim($text));
            $decoded = json_decode($text, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Gagal memproses format data dari AI.');
            }

            return $decoded;

        } catch (\Exception $e) {
            Log::error('AI Essay Generation Exception: '.$e->getMessage());
            throw $e;
        }
    }
}
