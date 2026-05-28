<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test teacher cannot register via public form.
     */
    public function test_teacher_cannot_register_manually()
    {
        $this->withoutMiddleware();
        $response = $this->post('/register', [
            'name' => 'Teacher Candidate',
            'email' => 'teacher@example.com',
            'role' => 'teacher',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => 'on',
        ]);

        // When validation fails in controller, it throws ValidationException
        // With withoutMiddleware, it might not redirect if the exception is not handled
        $this->assertDatabaseMissing('users', ['email' => 'teacher@example.com']);
    }

    /**
     * Test administrator can access user management and create teacher.
     */
    public function test_administrator_can_manage_users()
    {
        $this->withoutMiddleware();
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMINISTRATOR,
            'is_approved' => true
        ]);

        $this->actingAs($admin);

        // Can access index
        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(200);

        // Can store teacher
        $response = $this->post(route('admin.users.store'), [
            'name' => 'New Teacher',
            'email' => 'newteacher@example.com',
            'role' => 'teacher',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'newteacher@example.com',
            'role' => 'teacher',
            'is_approved' => true
        ]);
    }

    /**
     * Test administrator cannot manage superusers or other administrators.
     */
    public function test_administrator_cannot_manage_higher_roles()
    {
        $this->withoutMiddleware();
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMINISTRATOR,
            'is_approved' => true
        ]);

        $otherAdmin = User::factory()->create([
            'role' => User::ROLE_ADMINISTRATOR,
            'is_approved' => true
        ]);

        $superuser = User::factory()->create([
            'role' => User::ROLE_SUPERUSER,
            'is_approved' => true
        ]);

        $this->actingAs($admin);

        // Test logic in UserController index: Administrator cannot see other Admins or Superusers
        $response = $this->get(route('admin.users.index'));
        $usersInView = $response->viewData('users');
        
        $this->assertFalse($usersInView->contains($otherAdmin));
        $this->assertFalse($usersInView->contains($superuser));

        // Test protection in UserController@destroy
        $response = $this->delete(route('admin.users.destroy', $otherAdmin));
        $this->assertDatabaseHas('users', ['id' => $otherAdmin->id]);

        $response = $this->delete(route('admin.users.destroy', $superuser));
        $this->assertDatabaseHas('users', ['id' => $superuser->id]);
    }

    /**
     * Test isolation between two administrators.
     */
    public function test_administrator_isolation()
    {
        $this->withoutMiddleware();
        
        $admin1 = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR, 'is_approved' => true]);
        $admin2 = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR, 'is_approved' => true]);

        $studentOfAdmin1 = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'created_by_id' => $admin1->id
        ]);

        $teacherOfAdmin2 = User::factory()->create([
            'role' => User::ROLE_TEACHER,
            'created_by_id' => $admin2->id
        ]);

        // Admin 1 logged in
        $this->actingAs($admin1);

        // Should see their own student
        $response = $this->get(route('admin.users.index'));
        $usersInView = $response->viewData('users');
        $this->assertTrue($usersInView->contains($studentOfAdmin1));
        
        // Should NOT see admin2's teacher
        $this->assertFalse($usersInView->contains($teacherOfAdmin2));

        // Should NOT be able to delete admin2's teacher
        $response = $this->delete(route('admin.users.destroy', $teacherOfAdmin2));
        $this->assertDatabaseHas('users', ['id' => $teacherOfAdmin2->id]);
    }
}
