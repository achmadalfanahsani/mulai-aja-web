<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('question_responses', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('question_attempt_id')
                  ->constrained('question_attempts')
                  ->onDelete('cascade')
                  ->comment('Attempt yang memiliki response ini');
            
            $table->foreignId('question_id')
                  ->constrained('questions')
                  ->onDelete('cascade')
                  ->comment('Soal yang dijawab');
            
            // Answer
            $table->enum('selected_answer', ['A', 'B', 'C', 'D', 'E'])
                  ->nullable()
                  ->comment('Jawaban yang dipilih user. Null = tidak dijawab');
            
            // Grading
            $table->boolean('is_correct')
                  ->nullable()
                  ->comment('Apakah jawaban benar? Null = belum digrade');
            
            // Timing (per soal)
            $table->unsignedInteger('time_spent_seconds')
                  ->nullable()
                  ->comment('Waktu yang dihabiskan untuk soal ini');
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('question_attempt_id');
            $table->index('question_id');
            $table->unique(['question_attempt_id', 'question_id'])
                  ->comment('Satu attempt hanya bisa jawab satu soal sekali');
        });
    }

    public function down(): void {
        Schema::dropIfExists('question_responses');
    }
};
