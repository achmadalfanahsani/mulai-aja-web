<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ===== HELPER METHODS =====
    public function isAdmin(): bool {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool {
        return $this->role === 'student';
    }

    // ===== RELATIONSHIPS =====
    
    /**
     * Question packages yang dibuat oleh user ini
     * (Untuk teacher/admin yang membuat soal)
     */
    public function questionPackages() {
        return $this->hasMany(QuestionPackage::class);
    }

    /**
     * Question attempts yang dilakukan user ini
     * (Untuk student yang mengerjakan soal)
     */
    public function questionAttempts() {
        return $this->hasMany(QuestionAttempt::class);
    }
}
