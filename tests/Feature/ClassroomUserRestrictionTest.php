<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassroomUserRestrictionTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_only_see_students_they_created()
    {
        // 1. Setup two administrators
        $admin1 = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR, 'is_approved' => true]);
        $admin2 = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR, 'is_approved' => true]);

        // 2. Create students for each admin
        $student1 = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'is_approved' => true,
            'created_by_id' => $admin1->id
        ]);
        $student2 = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'is_approved' => true,
            'created_by_id' => $admin2->id
        ]);

        $classroom = Classroom::factory()->create();

        // 3. Admin 1 views classroom
        $response = $this->actingAs($admin1)->get(route('classrooms.show', $classroom));

        // 4. Verify Admin 1 only sees student 1, not student 2
        $response->assertStatus(200);
        $response->assertViewHas('availableStudents', function ($students) use ($student1, $student2) {
            return $students->contains($student1) && !$students->contains($student2);
        });
    }

    public function test_administrator_can_only_see_teachers_they_created()
    {
        // 1. Setup two administrators
        $admin1 = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR, 'is_approved' => true]);
        $admin2 = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR, 'is_approved' => true]);

        // 2. Create teachers for each admin
        $teacher1 = User::factory()->create([
            'role' => User::ROLE_TEACHER,
            'is_approved' => true,
            'created_by_id' => $admin1->id
        ]);
        $teacher2 = User::factory()->create([
            'role' => User::ROLE_TEACHER,
            'is_approved' => true,
            'created_by_id' => $admin2->id
        ]);

        $classroom = Classroom::factory()->create();

        // 3. Admin 1 views classroom
        $response = $this->actingAs($admin1)->get(route('classrooms.show', $classroom));

        // 4. Verify Admin 1 only sees teacher 1, not teacher 2
        $response->assertStatus(200);
        $response->assertViewHas('availableTeachers', function ($teachers) use ($teacher1, $teacher2) {
            return $teachers->contains($teacher1) && !$teachers->contains($teacher2);
        });
    }

    public function test_administrator_cannot_add_student_created_by_others()
    {
        $this->withoutMiddleware();
        $admin1 = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR, 'is_approved' => true]);
        $admin2 = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR, 'is_approved' => true]);

        $student2 = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'is_approved' => true,
            'created_by_id' => $admin2->id
        ]);

        $classroom = Classroom::factory()->create();

        $response = $this->actingAs($admin1)->post(route('classrooms.students.add', $classroom), [
            'user_id' => $student2->id
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('classroom_user', [
            'classroom_id' => $classroom->id,
            'user_id' => $student2->id
        ]);
    }

    public function test_administrator_cannot_add_teacher_created_by_others()
    {
        $this->withoutMiddleware();
        $admin1 = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR, 'is_approved' => true]);
        $admin2 = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR, 'is_approved' => true]);

        $teacher2 = User::factory()->create([
            'role' => User::ROLE_TEACHER,
            'is_approved' => true,
            'created_by_id' => $admin2->id
        ]);

        $classroom = Classroom::factory()->create();

        $response = $this->actingAs($admin1)->post(route('classrooms.teachers.add', $classroom), [
            'user_id' => $teacher2->id
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('classroom_teacher', [
            'classroom_id' => $classroom->id,
            'user_id' => $teacher2->id
        ]);
    }

    public function test_administrator_cannot_see_existing_members_created_by_others()
    {
        $admin1 = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR, 'is_approved' => true]);
        $admin2 = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR, 'is_approved' => true]);

        $student1 = User::factory()->create(['role' => User::ROLE_STUDENT, 'is_approved' => true, 'created_by_id' => $admin1->id]);
        $student2 = User::factory()->create(['role' => User::ROLE_STUDENT, 'is_approved' => true, 'created_by_id' => $admin2->id]);

        $classroom = Classroom::factory()->create();
        $classroom->students()->attach([$student1->id, $student2->id]);

        $response = $this->actingAs($admin1)->get(route('classrooms.show', $classroom));

        $response->assertStatus(200);
        $response->assertViewHas('classroom', function ($viewClassroom) use ($student1, $student2) {
            return $viewClassroom->students->contains($student1) && !$viewClassroom->students->contains($student2);
        });
    }

    public function test_superuser_can_see_all_students()
    {
        $superuser = User::factory()->create(['role' => User::ROLE_SUPERUSER, 'is_approved' => true]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR, 'is_approved' => true]);

        $student1 = User::factory()->create(['role' => User::ROLE_STUDENT, 'is_approved' => true, 'created_by_id' => $superuser->id]);
        $student2 = User::factory()->create(['role' => User::ROLE_STUDENT, 'is_approved' => true, 'created_by_id' => $admin->id]);

        $classroom = Classroom::factory()->create();

        $response = $this->actingAs($superuser)->get(route('classrooms.show', $classroom));

        $response->assertStatus(200);
        $response->assertViewHas('availableStudents', function ($students) use ($student1, $student2) {
            return $students->contains($student1) && $students->contains($student2);
        });
    }
}
