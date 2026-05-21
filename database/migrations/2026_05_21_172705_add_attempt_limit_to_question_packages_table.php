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
        Schema::table('question_packages', function (Blueprint $table) {
            $table->unsignedInteger('attempt_limit')
                  ->nullable()
                  ->default(null)
                  ->after('passing_score')
                  ->comment('Batas berapa kali siswa dapat mengerjakan paket ini. Null = tidak ada batas.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_packages', function (Blueprint $table) {
            $table->dropColumn('attempt_limit');
        });
    }
};
