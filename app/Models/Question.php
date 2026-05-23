<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'question_package_id',
        'question_type',
        'question_text',
        'explanation',
        'question_image_path',
        'correct_answer',
        'difficulty_level',
        'order',
        'is_active',
    ];

    const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    const TYPE_ESSAY = 'essay';

    public function isEssay(): bool {
        return $this->question_type === self::TYPE_ESSAY;
    }

    public function isMultipleChoice(): bool {
        return $this->question_type === self::TYPE_MULTIPLE_CHOICE;
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===== RELATIONSHIPS =====

    /**
     * Package yang memiliki soal ini
     */
    public function questionPackage(): BelongsTo {
        return $this->belongsTo(QuestionPackage::class);
    }

    /**
     * Opsi-opsi jawaban untuk soal ini
     */
    public function options(): HasMany {
        return $this->hasMany(QuestionOption::class);
    }

    /**
     * Responses dari user yang menjawab soal ini
     */
    public function responses(): HasMany {
        return $this->hasMany(QuestionResponse::class);
    }

    // ===== SCOPES =====

    public function scopeActive($query) {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query) {
        return $query->orderBy('order', 'asc');
    }

    // ===== HELPER METHODS =====

    /**
     * Dapatkan opsi yang sudah dikumpulkan dengan key A-E
     */
    public function getOptionsArray() {
        return $this->options()
            ->pluck('option_text', 'option_label')
            ->toArray();
    }

    /**
     * Check apakah soal punya image
     */
    public function hasImage(): bool {
        return !is_null($this->question_image_path);
    }

    /**
     * Get image URL
     */
    public function getImageUrl(): ?string {
        if ($this->hasImage()) {
            return '/storage/' . $this->question_image_path;
        }
        return null;
    }

    /**
     * Dapatkan statistics dari soal ini
     */
    public function getStatistics() {
        $totalResponses = $this->responses()->count();
        $correctResponses = $this->responses()
            ->where('is_correct', true)
            ->count();
        
        return [
            'total_responses' => $totalResponses,
            'correct_count' => $correctResponses,
            'accuracy_percentage' => $totalResponses > 0 
                ? round(($correctResponses / $totalResponses) * 100, 2)
                : 0,
        ];
    }
}
