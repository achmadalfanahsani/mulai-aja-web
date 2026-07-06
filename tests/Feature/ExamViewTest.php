<?php

namespace Tests\Feature;

use App\Models\QuestionPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_exams_index_shows_package_type_labels()
    {
        $user = User::factory()->superuser()->create();
        $this->actingAs($user);

        // Create packages of different types
        $packageMc = QuestionPackage::factory()->create([
            'name' => 'Paket Pilihan Ganda',
            'package_type' => 'multiple_choice',
            'user_id' => $user->id,
            'is_published' => true
        ]);
        
        $packageEssay = QuestionPackage::factory()->create([
            'name' => 'Paket Isian',
            'package_type' => 'essay',
            'user_id' => $user->id,
            'is_published' => true
        ]);

        $response = $this->get(route('exams.index'));

        $response->assertStatus(200);
        $response->assertSee('Paket Pilihan Ganda');
        $response->assertSee('Pilihan Ganda'); // Label
        $response->assertSee('bg-success'); // Badge class

        $response->assertSee('Paket Isian');
        $response->assertSee('Isian Singkat'); // Label
        $response->assertSee('bg-info'); // Badge class
    }

    public function test_teacher_and_administrator_cannot_access_exams_index()
    {
        $teacher = User::factory()->teacher()->create();
        $this->actingAs($teacher);
        $this->get(route('exams.index'))->assertStatus(403);

        $admin = User::factory()->administrator()->create();
        $this->actingAs($admin);
        $this->get(route('exams.index'))->assertStatus(403);
    }

    public function test_exams_index_shows_classroom_info()
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user);

        $classroom = \App\Models\Classroom::factory()->create([
            'name' => 'Kelas X RPL 1'
        ]);
        $classroom->students()->attach($user->id);

        $package = QuestionPackage::factory()->create([
            'name' => 'Ujian Akhir Semester',
            'is_published' => true,
            'user_id' => User::factory()->teacher()->create()->id
        ]);
        $package->classrooms()->attach($classroom->id);

        $response = $this->get(route('exams.index'));

        $response->assertStatus(200);
        $response->assertSee('Ujian Akhir Semester');
        $response->assertSee('Kelas X RPL 1');
        $response->assertSee('data-package-classrooms="Kelas X RPL 1"', false);
    }

    public function test_exam_attempt_page_does_not_contain_sidebar_and_profile_dropdown()
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user);

        $package = QuestionPackage::factory()->create([
            'duration_minutes' => 60,
            'is_published' => true,
        ]);

        $question = \App\Models\Question::create([
            'question_package_id' => $package->id,
            'question_text' => 'Sample Question',
            'question_type' => 'essay',
            'correct_answer' => 'Sample Answer',
            'order' => 1,
        ]);

        $attempt = \App\Models\QuestionAttempt::create([
            'user_id' => $user->id,
            'question_package_id' => $package->id,
            'started_at' => now(),
            'is_completed' => false,
        ]);

        // Inisialisasi draft response kosong agar views/exams/attempt tidak error
        \App\Models\QuestionResponse::create([
            'question_attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'question_snapshot' => $question->toJson(),
            'selected_answer' => null,
            'is_correct' => null,
        ]);

        $response = $this->withSession([
            "attempt_{$attempt->id}_questions" => [$question->id],
            "attempt_{$attempt->id}_options" => [$question->id => ['A', 'B', 'C', 'D']],
        ])->get(route('exams.attempt', $attempt->id));

        $response->assertStatus(200);
        // Sidebar element is not rendered
        $response->assertDontSee('<nav id="sidebar">', false);
        // User profile dropdown is not rendered
        $response->assertDontSee('id="page-header-user-dropdown"', false);
        // Color themes selection is still rendered
        $response->assertSee('data-toggle="theme"', false);
    }
}
