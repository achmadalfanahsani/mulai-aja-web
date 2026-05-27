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
        Schema::table('users', function (Blueprint $table) {
            $table->index('is_approved');
        });

        Schema::table('question_packages', function (Blueprint $table) {
            $table->index('package_type');
        });

        Schema::table('question_attempts', function (Blueprint $table) {
            $table->index('is_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_approved']);
        });

        Schema::table('question_packages', function (Blueprint $table) {
            $table->dropIndex(['package_type']);
        });

        Schema::table('question_attempts', function (Blueprint $table) {
            $table->dropIndex(['is_completed']);
        });
    }
};
