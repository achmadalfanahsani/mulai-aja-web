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
     * Ekstrak teks JSON dari response Gemini API.
     * Model "thinking" (gemini-2.5-*) mengembalikan multiple parts:
     *   - parts dengan "thought": true  → proses berpikir (SKIP)
     *   - parts tanpa "thought"         → respons final (AMBIL INI)
     * Model non-thinking (gemini-1.5-*) hanya memiliki 1 part.
     */
    protected function extractJsonText(array $data): ?string
    {
        $parts = $data['candidates'][0]['content']['parts'] ?? [];

        Log::info('Gemini API Response parts count: ' . count($parts));

        // Cari part yang BUKAN thought (respons final)
        $responsePart = null;
        foreach ($parts as $index => $part) {
            $isThought = $part['thought'] ?? false;
            Log::info("Part[$index]: thought=" . ($isThought ? 'true' : 'false') . ", text_length=" . strlen($part['text'] ?? ''));

            if (!$isThought && isset($part['text'])) {
                $responsePart = $part['text'];
                // Jangan break, ambil part terakhir yang bukan thought
            }
        }

        // Fallback: jika tidak ada part non-thought, gunakan part terakhir
        if ($responsePart === null && !empty($parts)) {
            $lastPart = end($parts);
            $responsePart = $lastPart['text'] ?? null;
            Log::warning('Gemini: No non-thought part found, using last part as fallback.');
        }

        return $responsePart;
    }

    /**
     * Hapus soal duplikat berdasarkan question_text.
     */
    protected function deduplicate(array $questions): array
    {
        $seen = [];
        $unique = [];

        foreach ($questions as $q) {
            // Normalisasi: lowercase dan hilangkan whitespace berlebih
            $key = strtolower(trim(preg_replace('/\s+/', ' ', $q['question_text'] ?? '')));

            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $q;
            } else {
                Log::warning('Gemini: Duplikasi soal terdeteksi dan dihapus: ' . ($q['question_text'] ?? ''));
            }
        }

        return $unique;
    }

    /**
     * Kirim request ke Gemini API dan parse respons JSON.
     */
    protected function callGeminiApi(string $prompt): array
    {
        $url = "{$this->baseUrl}/models/{$this->model}:generateContent";

        // Konfigurasi khusus untuk thinking model (gemini-2.5-*)
        $generationConfig = [
            'response_mime_type' => 'application/json',
        ];

        $requestBody = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => $generationConfig,
        ];

        $response = Http::timeout(120)->withHeaders([
            'Content-Type' => 'application/json',
            'x-goog-api-key' => $this->apiKey,
        ])->post($url, $requestBody);

        if ($response->failed()) {
            $status = $response->status();
            $body = $response->body();
            Log::error("Gemini API Error [$status]: " . $body);

            if ($status === 404) {
                throw new \Exception("Model '{$this->model}' tidak ditemukan atau endpoint salah (404). URL: $url.");
            }

            throw new \Exception('AI Service Error: ' . ($response->json('error.message') ?? "Status $status"));
        }

        $data = $response->json();

        // Gunakan extractJsonText untuk menangani thinking model dengan benar
        $text = $this->extractJsonText($data);

        if (!$text) {
            Log::error('Gemini API Invalid Response Structure: ' . json_encode($data));
            throw new \Exception('Struktur respon dari AI tidak valid.');
        }

        Log::info('Gemini API raw text response (first 500 chars): ' . substr($text, 0, 500));

        // Terkadang model membungkus dengan ```json ... ```
        $text = preg_replace('/^```json\s*|\s*```$/', '', trim($text));

        $decoded = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON Decode Error: ' . json_last_error_msg() . ' | Text: ' . $text);
            throw new \Exception('Gagal memproses format data dari AI.');
        }

        // Selalu deduplikasi hasilnya
        return $this->deduplicate($decoded);
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
5. SANGAT PENTING: JANGAN PERNAH menduplikasi soal. Jumlah soal di JSON hasil (output) harus SAMA PERSIS dengan jumlah soal yang diberikan di input. Tidak boleh ada soal yang ganda/berulang.

Pertanyaan:
$rawText
PROMPT;

        try {
            return $this->callGeminiApi($prompt);
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
4. SANGAT PENTING: JANGAN PERNAH menduplikasi soal. Jumlah soal di JSON hasil (output) harus SAMA PERSIS dengan jumlah soal yang diberikan di input. Tidak boleh ada soal yang ganda/berulang.

Pertanyaan:
$rawText
PROMPT;

        try {
            return $this->callGeminiApi($prompt);
        } catch (\Exception $e) {
            Log::error('AI Essay Generation Exception: '.$e->getMessage());
            throw $e;
        }
    }
}
