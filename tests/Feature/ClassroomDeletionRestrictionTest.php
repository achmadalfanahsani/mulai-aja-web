<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Classroom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class ClassroomDeletionRestrictionTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_teacher_cannot_delete_classroom()
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER, 'is_approved' => true]);
        $classroom = Classroom::factory()->create();
        $classroom->teachers()->attach($teacher->id);

        // Ensure teacher can see the classroom but not the delete button
        $response = $this->actingAs($teacher)->get(route('classrooms.index'));
        $response->assertStatus(200);
        $response->assertDontSee('modal-delete-' . $classroom->id);

        // Ensure teacher cannot delete via the route
        $response = $this->actingAs($teacher)->delete(route('classrooms.destroy', $classroom->id));
        $response->assertStatus(403);
        
        $this->assertDatabaseHas('classrooms', ['id' => $classroom->id, 'deleted_at' => null]);
    }

    public function test_administrator_can_delete_classroom_they_created()
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR, 'is_approved' => true]);
        $classroom = Classroom::factory()->create(['created_by_id' => $admin->id]);

        // Ensure admin can see the delete button
        $response = $this->actingAs($admin)->get(route('classrooms.index'));
        $response->assertStatus(200);
        $response->assertSee('modal-delete-' . $classroom->id);

        // Ensure admin can delete via the route
        $response = $this->actingAs($admin)->delete(route('classrooms.destroy', $classroom->id));
        $response->assertRedirect(route('classrooms.index'));
        
        $this->assertSoftDeleted('classrooms', ['id' => $classroom->id]);
    }

    public function test_superuser_can_delete_classroom()
    {
        $superuser = User::factory()->create(['role' => User::ROLE_SUPERUSER]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
        $classroom = Classroom::factory()->create(['created_by_id' => $admin->id]);

        // Ensure superuser can see the delete button
        $response = $this->actingAs($superuser)->get(route('classrooms.index'));
        $response->assertStatus(200);
        $response->assertSee('modal-delete-' . $classroom->id);

        // Ensure superuser can delete via the route
        $response = $this->actingAs($superuser)->delete(route('classrooms.destroy', $classroom->id));
        $response->assertRedirect(route('classrooms.index'));
        
        $this->assertSoftDeleted('classrooms', ['id' => $classroom->id]);
    }
}
