<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionResponse extends Model {
    use HasFactory;

    protected $fillable = [
        'question_attempt_id',
        'question_id',
        'question_snapshot',
        'selected_answer',
        'essay_answer',
        'is_correct',
        'time_spent_seconds',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'question_snapshot' => 'array',
    ];

    // ===== RELATIONSHIPS =====

    /**
     * Attempt yang memiliki response ini
     */
    public function questionAttempt(): BelongsTo {
        return $this->belongsTo(QuestionAttempt::class);
    }

    /**
     * Soal yang dijawab
     */
    public function question(): BelongsTo {
        return $this->belongsTo(Question::class)->withTrashed();
    }

    /**
     * Helper untuk mengambil data soal (dari relasi atau snapshot)
     */
    public function getQuestionData() {
        if ($this->question) {
            return $this->question;
        }
        
        // Return sebagai objek agar kompatibel dengan pemanggilan $resp->question->...
        return (object) ($this->question_snapshot ?? []);
    }

    // ===== HELPER METHODS =====

    /**
     * Check apakah sudah di-grade
     */
    public function isGraded(): bool {
        return !is_null($this->is_correct);
    }

    /**
     * Check apakah user tidak menjawab
     */
    public function isUnanswered(): bool {
        $question = $this->getQuestionData();
        
        // Cek jika pertanyaan tipe pilihan ganda (cek dari snapshot atau relasi)
        $isMC = isset($question->question_type) ? $question->question_type === 'multiple_choice' : ($question instanceof \App\Models\Question && $question->isMultipleChoice());
        
        if ($isMC) {
            return is_null($this->selected_answer);
        }
        return is_null($this->essay_answer) || trim($this->essay_answer) === '';
    }

    /**
     * Get the correct answer dari question
     */
    public function getCorrectAnswer(): string {
        $question = $this->getQuestionData();
        return $question->correct_answer ?? '';
    }

    /**
     * Get selected answer text
     */
    public function getSelectedAnswerText(): ?string {
        if ($this->isUnanswered()) {
            return null;
        }

        $question = $this->getQuestionData();
        
        $isEssay = isset($question->question_type) ? $question->question_type === 'essay' : ($question instanceof \App\Models\Question && $question->isEssay());

        if ($isEssay) {
            return $this->essay_answer;
        }
        
        // Jika dari relasi Question, ambil opsi dari database
        if ($question instanceof \App\Models\Question) {
            return $question->options()
                ->where('option_label', $this->selected_answer)
                ->value('option_text');
        }

        // Jika dari snapshot, ambil dari opsi di dalam snapshot
        $options = $question->options ?? [];
        return collect($options)->firstWhere('option_label', $this->selected_answer)['option_text'] ?? null;
    }

    /**
     * Get correct answer text
     */
    public function getCorrectAnswerText(): string {
        $question = $this->getQuestionData();

        $isEssay = isset($question->question_type) ? $question->question_type === 'essay' : ($question instanceof \App\Models\Question && $question->isEssay());

        if ($isEssay) {
            return $question->correct_answer ?? '';
        }

        if ($question instanceof \App\Models\Question) {
            return $question->options()
                ->where('option_label', $this->getCorrectAnswer())
                ->value('option_text') ?? '';
        }

        $options = $question->options ?? [];
        return collect($options)->firstWhere('option_label', $this->getCorrectAnswer())['option_text'] ?? '';
    }
}
