<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            
            // Foreign key
            $table->foreignId('question_id')
                  ->constrained('questions')
                  ->onDelete('cascade')
                  ->comment('Soal yang memiliki opsi ini');
            
            // Option content
            $table->enum('option_label', ['A', 'B', 'C', 'D', 'E'])
                  ->comment('Label opsi jawaban');
            
            $table->text('option_text')
                  ->comment('Isi dari opsi jawaban');
            
            // Timestamps
            $table->timestamps();
            
            // Constraints & Indexes
            $table->unique(['question_id', 'option_label'])
                  ->comment('Satu question hanya bisa punya satu opsi per label');
            
            $table->index('question_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('question_options');
    }
};
