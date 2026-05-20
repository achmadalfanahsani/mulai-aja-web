<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionPackage extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'duration_minutes',
        'is_published',
        'shuffle_questions',
        'shuffle_answers',
        'passing_score',
        'total_questions_count',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'shuffle_questions' => 'boolean',
        'shuffle_answers' => 'boolean',
    ];

    // ===== RELATIONSHIPS =====

    /**
     * User yang membuat paket soal ini
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * Soal-soal dalam paket ini
     */
    public function questions(): HasMany {
        return $this->hasMany(Question::class);
    }

    /**
     * Questions yang aktif saja
     */
    public function activeQuestions(): HasMany {
        return $this->questions()->where('is_active', true);
    }

    /**
     * Percobaan mengerjakan paket ini
     */
    public function attempts(): HasMany {
        return $this->hasMany(QuestionAttempt::class);
    }

    // ===== SCOPES =====

    public function scopePublished($query) {
        return $query->where('is_published', true);
    }

    public function scopeNotPublished($query) {
        return $query->where('is_published', false);
    }

    public function scopeByUser($query, $userId) {
        return $query->where('user_id', $userId);
    }

    // ===== HELPER METHODS =====

    /**
     * Dapatkan jumlah soal aktif
     */
    public function getActiveQuestionsCount(): int {
        return $this->activeQuestions()->count();
    }

    /**
     * Validasi: minimal berapa soal?
     */
    public function hasMinimumQuestions(): bool {
        return $this->getActiveQuestionsCount() >= 1;
    }

    /**
     * Format durasi untuk display
     */
    public function getFormattedDuration(): string {
        if (is_null($this->duration_minutes)) {
            return 'N/A';
        }
        
        $totalMinutes = abs((int) $this->duration_minutes);
        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;
        
        $parts = [];
        if ($hours > 0) {
            $parts[] = "{$hours} Jam";
        }
        if ($minutes > 0) {
            $parts[] = "{$minutes} Menit";
        }
        
        return empty($parts) ? '0 Menit' : implode(' ', $parts);
    }

    /**
     * Dapatkan total attempts
     */
    public function getTotalAttempts(): int {
        return $this->attempts()->count();
    }

    /**
     * Dapatkan rata-rata score
     */
    public function getAverageScore(): float {
        return $this->attempts()
            ->whereNotNull('total_score')
            ->avg('total_score') ?? 0;
    }
}
