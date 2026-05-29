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
        'created_by_id',
        'description',
    ];

    /**
     * Guru yang mengelola kelas ini
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'classroom_teacher')
                    ->withTimestamps();
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

    /**
     * User (Admin/Superuser) pembuat kelas ini
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
