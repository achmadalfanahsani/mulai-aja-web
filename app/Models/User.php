<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable {
    use HasFactory, Notifiable;

    /**
     * Role Constants
     */
    public const ROLE_STUDENT = 'student';
    public const ROLE_TEACHER = 'teacher';
    public const ROLE_ADMINISTRATOR = 'administrator';
    public const ROLE_SUPERUSER = 'superuser';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_approved',
        'created_by_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved' => 'boolean',
        ];
    }

    // ===== HELPER METHODS =====

    public function isStudent(): bool {
        return $this->role === self::ROLE_STUDENT;
    }

    public function isTeacher(): bool {
        return $this->role === self::ROLE_TEACHER;
    }

    public function isAdministrator(): bool {
        return $this->role === self::ROLE_ADMINISTRATOR;
    }

    /**
     * Get route prefix for user management
     */
    public function getRoutePrefix(): string {
        return $this->isSuperuser() ? 'superuser' : 'admin';
    }

    public function setRole(string $role): void {
        $this->role = $role;
        $this->save();
    }

    /**
     * Check if user is approved
     */
    public function isApproved(): bool {
        return $this->is_approved === true;
    }

    // ===== SCOPES =====

    public function scopePendingApproval(Builder $query): void {
        $query->whereIn('role', [self::ROLE_ADMINISTRATOR, self::ROLE_TEACHER])
              ->where('is_approved', false);
    }

    // ===== RELATIONSHIPS =====

    /**
     * User yang membuat user ini (Administrator/Superuser)
     */
    public function creator() {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * User yang dibuat oleh user ini
     */
    public function createdUsers() {
        return $this->hasMany(User::class, 'created_by_id');
    }
    
    /**
     * Question packages yang dibuat oleh user ini
     */
    public function questionPackages() {
        return $this->hasMany(QuestionPackage::class);
    }

    /**
     * Question attempts yang dilakukan user ini
     */
    public function questionAttempts() {
        return $this->hasMany(QuestionAttempt::class);
    }

    /**
     * Kelas di mana user ini menjadi anggotanya (untuk Student)
     */
    public function classrooms() {
        return $this->belongsToMany(Classroom::class, 'classroom_user')
                    ->withTimestamps();
    }

    /**
     * Kelas yang dikelola oleh user ini (untuk Teacher)
     */
    public function managedClassrooms() {
        return $this->hasMany(Classroom::class, 'teacher_id');
    }
}
