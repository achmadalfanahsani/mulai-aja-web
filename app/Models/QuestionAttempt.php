<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionAttempt extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_package_id',
        'started_at',
        'finished_at',
        'time_spent_seconds',
        'total_score',
        'is_auto_submitted',
        'is_completed',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'is_auto_submitted' => 'boolean',
        'is_completed' => 'boolean',
    ];

    // ===== RELATIONSHIPS =====

    /**
     * Student yang mengerjakan attempt ini
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * Package yang dikerjakan
     */
    public function questionPackage(): BelongsTo {
        return $this->belongsTo(QuestionPackage::class);
    }

    /**
     * Responses (jawaban) dalam attempt ini
     */
    public function responses(): HasMany {
        return $this->hasMany(QuestionResponse::class);
    }

    // ===== SCOPES =====

    public function scopeCompleted($query) {
        return $query->where('is_completed', true);
    }

    public function scopeInProgress($query) {
        return $query->whereNull('finished_at');
    }

    // ===== HELPER METHODS =====

    /**
     * Check apakah attempt sudah selesai
     */
    public function isFinished(): bool {
        return !is_null($this->finished_at);
    }

    /**
     * Check apakah sudah expired berdasarkan durasi package
     */
    public function isExpired(): bool {
        if (!$this->isFinished()) {
            $maxDuration = $this->questionPackage->duration_minutes * 60; // to seconds
            $elapsed = now()->diffInSeconds($this->started_at);
            return $elapsed > $maxDuration;
        }
        return false;
    }

    /**
     * Dapatkan waktu sisa dalam seconds
     */
    public function getTimeRemaining(): int {
        $maxDuration = $this->questionPackage->duration_minutes * 60;
        $elapsed = now()->diffInSeconds($this->started_at);
        $remaining = $maxDuration - $elapsed;
        
        return max(0, $remaining);
    }

    /**
     * Dapatkan statistik jawaban
     */
    public function getAnswerStatistics() {
        // Gunakan jumlah response yang ada di database untuk attempt ini
        $totalResponses = $this->responses()->count();
        
        $answeredCount = $this->responses()
            ->where(function($query) {
                $query->whereNotNull('selected_answer')
                    ->orWhere(function($q) {
                        $q->whereNotNull('essay_answer')
                          ->where('essay_answer', '!=', '');
                    });
            })
            ->count();
            
        $correctCount = $this->responses()
            ->where('is_correct', true)
            ->count();
            
        $wrongCount = $this->responses()
            ->where('is_correct', false)
            ->where(function($query) {
                $query->whereNotNull('selected_answer')
                    ->orWhere(function($q) {
                        $q->whereNotNull('essay_answer')
                          ->where('essay_answer', '!=', '');
                    });
            })
            ->count();

        return [
            'total_questions' => $totalResponses,
            'answered_count' => $answeredCount,
            'unanswered_count' => max(0, $totalResponses - $answeredCount),
            'correct_count' => $correctCount,
            'wrong_count' => $wrongCount,
        ];
    }

    /**
     * Format durasi pengerjaan
     */
    public function getFormattedDuration(): string {
        if (is_null($this->time_spent_seconds)) {
            return 'N/A';
        }
        
        $totalSeconds = abs((int) $this->time_spent_seconds);
        $hours = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);
        $seconds = $totalSeconds % 60;
        
        $parts = [];
        if ($hours > 0) {
            $parts[] = "{$hours} Jam";
        }
        if ($minutes > 0) {
            $parts[] = "{$minutes} Menit";
        }
        if ($seconds > 0 || empty($parts)) {
            $parts[] = "{$seconds} Detik";
        }
        
        return implode(' ', $parts);
    }

    /**
     * Check apakah lulus (vs passing_score)
     */
    public function isPassed(): ?bool {
        if (is_null($this->total_score)) {
            return null;
        }

        if (is_null($this->questionPackage)) {
            return false; // Anggap tidak lulus jika paket sudah dihapus
        }
        
        $passingScore = $this->questionPackage->passing_score ?? 0;
        
        return $this->total_score >= $passingScore;
    }
}
