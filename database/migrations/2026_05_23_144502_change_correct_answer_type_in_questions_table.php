<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->text('correct_answer')->change()->comment('Jawaban yang benar (Label A-E untuk pilihan ganda, atau teks untuk isian)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('correct_answer', ['A', 'B', 'C', 'D', 'E'])->change()->comment('Jawaban yang benar');
        });
    }
};
