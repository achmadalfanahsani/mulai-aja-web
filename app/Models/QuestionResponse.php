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
        'selected_answer',
        'essay_answer',
        'is_correct',
        'time_spent_seconds',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
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
        return $this->belongsTo(Question::class);
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
        if ($this->question->isMultipleChoice()) {
            return is_null($this->selected_answer);
        }
        return is_null($this->essay_answer) || trim($this->essay_answer) === '';
    }

    /**
     * Get the correct answer dari question
     */
    public function getCorrectAnswer(): string {
        return $this->question->correct_answer;
    }

    /**
     * Get selected answer text
     */
    public function getSelectedAnswerText(): ?string {
        if ($this->isUnanswered()) {
            return null;
        }

        if ($this->question->isEssay()) {
            return $this->essay_answer;
        }
        
        return $this->question
            ->options()
            ->where('option_label', $this->selected_answer)
            ->value('option_text');
    }

    /**
     * Get correct answer text
     */
    public function getCorrectAnswerText(): string {
        if ($this->question->isEssay()) {
            return $this->question->correct_answer;
        }

        return $this->question
            ->options()
            ->where('option_label', $this->getCorrectAnswer())
            ->value('option_text') ?? '';
    }
}
