<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Classroom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassroomManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_cannot_create_classroom()
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER, 'is_approved' => true]);

        $response = $this->actingAs($teacher)->get(route('classrooms.create'));
        $response->assertStatus(403);

        $response = $this->actingAs($teacher)->post(route('classrooms.store'), [
            'name' => 'New Classroom',
            'description' => 'Description'
        ]);
        $response->assertStatus(403);
    }

    public function test_administrator_can_create_classroom()
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR, 'is_approved' => true]);

        $response = $this->actingAs($admin)->get(route('classrooms.create'));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->post(route('classrooms.store'), [
            'name' => 'Admin Classroom',
            'description' => 'Created by admin'
        ]);
        
        $response->assertRedirect(route('classrooms.index'));
        $this->assertDatabaseHas('classrooms', ['name' => 'Admin Classroom']);
    }

    public function test_administrator_can_assign_multiple_teachers_to_classroom()
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR, 'is_approved' => true]);
        $teacher1 = User::factory()->create(['role' => User::ROLE_TEACHER, 'is_approved' => true]);
        $teacher2 = User::factory()->create(['role' => User::ROLE_TEACHER, 'is_approved' => true]);
        $classroom = Classroom::factory()->create();

        $response = $this->actingAs($admin)->post(route('classrooms.teachers.add', $classroom->id), [
            'user_id' => $teacher1->id
        ]);
        $response->assertSessionHas('success');

        $response = $this->actingAs($admin)->post(route('classrooms.teachers.add', $classroom->id), [
            'user_id' => $teacher2->id
        ]);
        $response->assertSessionHas('success');

        $this->assertEquals(2, $classroom->fresh()->teachers()->count());
    }

    public function test_teacher_can_view_assigned_classroom()
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER, 'is_approved' => true]);
        $classroom = Classroom::factory()->create();
        $classroom->teachers()->attach($teacher->id);

        $response = $this->actingAs($teacher)->get(route('classrooms.index'));
        $response->assertStatus(200);
        $response->assertSee($classroom->name);

        $response = $this->actingAs($teacher)->get(route('classrooms.show', $classroom->id));
        $response->assertStatus(200);
    }

    public function test_teacher_cannot_view_unassigned_classroom()
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER, 'is_approved' => true]);
        $classroom = Classroom::factory()->create();

        $response = $this->actingAs($teacher)->get(route('classrooms.index'));
        $response->assertStatus(200);
        $response->assertDontSee($classroom->name);

        $response = $this->actingAs($teacher)->get(route('classrooms.show', $classroom->id));
        $response->assertStatus(403);
    }

    public function test_superuser_can_see_classroom_creator_info()
    {
        $superuser = User::factory()->create(['role' => User::ROLE_SUPERUSER]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR, 'name' => 'Admin User']);
        $classroom = Classroom::factory()->create(['created_by_id' => $admin->id]);

        $response = $this->actingAs($superuser)->get(route('classrooms.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Dibuat Oleh');
        $response->assertSee('Admin User');
    }

    public function test_administrator_cannot_see_classroom_creator_info()
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR, 'is_approved' => true]);
        $classroom = Classroom::factory()->create(['created_by_id' => $admin->id]);

        $response = $this->actingAs($admin)->get(route('classrooms.index'));
        
        $response->assertStatus(200);
        $response->assertDontSee('Dibuat Oleh');
    }
}
