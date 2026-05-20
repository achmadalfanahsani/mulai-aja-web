<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            
            // Foreign key
            $table->foreignId('question_package_id')
                  ->constrained('question_packages')
                  ->onDelete('cascade')
                  ->comment('Paket soal yang memuat soal ini');
            
            // Question content
            $table->longText('question_text')
                  ->comment('Isi pertanyaan/soal');
            
            $table->longText('explanation')
                  ->nullable()
                  ->comment('Penjelasan atau kunci jawaban (opsional)');
            
            $table->string('question_image_path', 500)
                  ->nullable()
                  ->comment('Path gambar penjelas soal (opsional). Format: questions/package_id/filename');
            
            // Answer & Difficulty
            $table->enum('correct_answer', ['A', 'B', 'C', 'D', 'E'])
                  ->comment('Jawaban yang benar');
            
            $table->enum('difficulty_level', ['easy', 'medium', 'hard'])
                  ->nullable()
                  ->comment('Level kesulitan untuk analisis (opsional)');
            
            // Order & Status
            $table->unsignedInteger('order')
                  ->default(0)
                  ->comment('Urutan soal di dalam paket');
            
            $table->boolean('is_active')
                  ->default(true)
                  ->comment('Apakah soal aktif (soft delete alternative)');
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('question_package_id');
            $table->index('is_active');
            $table->index('order');
        });
    }

    public function down(): void {
        Schema::dropIfExists('questions');
    }
};
