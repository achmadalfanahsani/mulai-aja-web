<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('question_packages', function (Blueprint $table) {
            $table->id();
            
            // Foreign key
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->comment('Siapa yang membuat paket soal');
            
            // Main fields
            $table->string('name', 255)
                  ->comment('Nama paket soal');
            
            $table->text('description')
                  ->nullable()
                  ->comment('Deskripsi paket soal');
            
            $table->integer('duration_minutes')
                  ->comment('Durasi pengerjaan dalam menit. Max 480 (8 jam)');
            
            // Status & Configuration
            $table->boolean('is_published')
                  ->default(false)
                  ->comment('Apakah paket sudah dipublikasi untuk students');
            
            $table->boolean('shuffle_questions')
                  ->default(true)
                  ->comment('Acak urutan soal?');
            
            $table->boolean('shuffle_answers')
                  ->default(true)
                  ->comment('Acak urutan opsi jawaban?');
            
            $table->unsignedTinyInteger('passing_score')
                  ->nullable()
                  ->default(null)
                  ->comment('Nilai minimum kelulusan (0-100). Null = tidak ada batasan');
            
            // Cache columns untuk performa
            $table->unsignedInteger('total_questions_count')
                  ->default(0)
                  ->comment('Cache jumlah soal di paket ini');
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes()
                  ->comment('Soft delete untuk audit trail');
            
            // Indexes
            $table->index('user_id');
            $table->index('is_published');
            $table->index('created_at');
        });
    }

    public function down(): void {
        Schema::dropIfExists('question_packages');
    }
};
