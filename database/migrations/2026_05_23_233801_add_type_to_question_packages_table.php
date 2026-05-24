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
            $table->string('package_type')->default('multiple_choice')->after('description')->comment('Tipe paket soal: multiple_choice, essay, mixed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_packages', function (Blueprint $table) {
            $table->dropColumn('package_type');
        });
    }
};
