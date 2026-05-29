<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('classroom_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // teacher
            $table->timestamps();
            
            $table->unique(['classroom_id', 'user_id']);
        });

        // Migrasi data dari classrooms.teacher_id ke classroom_teacher
        $classrooms = DB::table('classrooms')->whereNotNull('teacher_id')->get();
        foreach ($classrooms as $classroom) {
            DB::table('classroom_teacher')->insert([
                'classroom_id' => $classroom->id,
                'user_id' => $classroom->teacher_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Hapus kolom teacher_id setelah migrasi data
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
        });

        // Kembalikan data (opsional, hanya guru pertama jika ada)
        $pivotData = DB::table('classroom_teacher')->orderBy('id')->get();
        foreach ($pivotData as $data) {
            DB::table('classrooms')
                ->where('id', $data->classroom_id)
                ->whereNull('teacher_id')
                ->update(['teacher_id' => $data->user_id]);
        }

        Schema::dropIfExists('classroom_teacher');
    }
};
