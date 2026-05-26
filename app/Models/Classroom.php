<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Classroom extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'teacher_id',
        'description',
    ];

    /**
     * Guru yang mengelola kelas ini
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Siswa yang tergabung dalam kelas ini
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'classroom_user')
                    ->withTimestamps();
    }

    /**
     * Paket soal yang ditugaskan ke kelas ini
     */
    public function questionPackages(): BelongsToMany
    {
        return $this->belongsToMany(QuestionPackage::class, 'classroom_question_package')
                    ->withTimestamps();
    }
}
