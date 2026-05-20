<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('question_attempts', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->comment('Student yang mengerjakan');
            
            $table->foreignId('question_package_id')
                  ->constrained('question_packages')
                  ->onDelete('cascade')
                  ->comment('Paket soal yang dikerjakan');
            
            // Timing
            $table->timestamp('started_at')
                  ->comment('Waktu mulai mengerjakan');
            
            $table->timestamp('finished_at')
                  ->nullable()
                  ->comment('Waktu selesai mengerjakan');
            
            // Results
            $table->unsignedInteger('time_spent_seconds')
                  ->nullable()
                  ->comment('Total waktu pengerjaan dalam detik');
            
            $table->unsignedTinyInteger('total_score')
                  ->nullable()
                  ->default(null)
                  ->comment('Skor akhir (0-100 atau null jika belum selesai)');
            
            $table->boolean('is_auto_submitted')
                  ->default(false)
                  ->comment('Apakah auto-submit karena waktu habis?');
            
            // Flags
            $table->boolean('is_completed')
                  ->default(false)
                  ->comment('Apakah attempt sudah complete dan scored?');
            
            // Timestamps
            $table->timestamps();
            
            // Indexes untuk query cepat
            $table->index('user_id');
            $table->index('question_package_id');
            $table->index(['user_id', 'question_package_id']);
            $table->index('created_at');
        });
    }

    public function down(): void {
        Schema::dropIfExists('question_attempts');
    }
};
