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
        // 1. Add new columns if they don't exist
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role_new')) {
                $table->enum('role_new', ['student', 'teacher', 'administrator', 'superuser'])
                      ->default('student')
                      ->after('password');
            }
            if (!Schema::hasColumn('users', 'is_approved')) {
                $table->boolean('is_approved')->default(true)->after('role_new');
            }
        });

        // 2. Drop old column and its index if they exist
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                // Check if index exists - generic way for SQLite/MySQL
                try {
                    $table->dropIndex(['role']);
                } catch (\Exception $e) {
                    // Index might not exist or have different name
                }
                $table->dropColumn('role');
            }
        });

        // 3. Rename new column to 'role'
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role_new') && !Schema::hasColumn('users', 'role')) {
                $table->renameColumn('role_new', 'role');
            }
        });

        // 4. Add index to the new role column
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                try { $table->dropIndex(['role']); } catch (\Exception $e) {}
                $table->dropColumn(['role', 'is_approved']);
            }
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'teacher', 'student'])->default('student')->after('password');
            $table->index('role');
        });
    }
};
