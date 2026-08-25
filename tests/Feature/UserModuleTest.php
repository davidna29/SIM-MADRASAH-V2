<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
    }

    protected function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@madrasah.sch.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'guru',
        ], $overrides);
    }

    public function test_admin_can_view_user_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('pengguna.index'));

        $response->assertOk();
        $response->assertSee('Pengguna & Role');
    }

    public function test_admin_can_create_user(): void
    {
        $response = $this->actingAs($this->admin)->post(route('pengguna.store'), $this->payload());

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'test@madrasah.sch.id', 'role' => 'guru']);
    }

    public function test_admin_can_create_user_with_additional_roles(): void
    {
        $response = $this->actingAs($this->admin)->post(route('pengguna.store'), $this->payload([
            'additional_roles' => ['wakamad_kurikulum', 'tata_usaha'],
        ]));

        $response->assertRedirect();

        $user = User::where('email', 'test@madrasah.sch.id')->first();
        $this->assertCount(2, $user->userRoles);
    }

    public function test_admin_can_update_user(): void
    {
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($this->admin)->put(route('pengguna.update', $user), $this->payload([
            'name' => 'Updated Name',
            'role' => 'wakamad_kurikulum',
        ]));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name', 'role' => 'wakamad_kurikulum']);
    }

    public function test_admin_can_update_user_password(): void
    {
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($this->admin)->put(route('pengguna.update', $user), $this->payload([
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]));

        $response->assertRedirect();
        $this->assertTrue(\Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_admin_can_soft_delete_user(): void
    {
        $user = User::factory()->create(['role' => 'guru']);

        $this->actingAs($this->admin)->delete(route('pengguna.destroy', $user));

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('pengguna.destroy', $this->admin));

        $response->assertForbidden();
    }

    public function test_admin_cannot_delete_last_super_admin(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('pengguna.destroy', $this->admin));

        $response->assertForbidden();
    }

    public function test_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'existing@madrasah.sch.id']);

        $response = $this->actingAs($this->admin)
            ->from(route('pengguna.create'))
            ->post(route('pengguna.store'), $this->payload(['email' => 'existing@madrasah.sch.id']));

        $response->assertSessionHasErrors('email');
    }

    public function test_password_must_be_confirmed(): void
    {
        $response = $this->actingAs($this->admin)->post(route('pengguna.store'), $this->payload([
            'password_confirmation' => 'mismatch',
        ]));

        $response->assertSessionHasErrors('password');
    }

    public function test_teacher_cannot_access_user_module(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);

        $this->actingAs($teacher)->get(route('pengguna.index'))->assertForbidden();
        $this->actingAs($teacher)->get(route('pengguna.create'))->assertForbidden();
    }

    public function test_unauthenticated_redirects_to_login(): void
    {
        $this->get(route('pengguna.index'))->assertRedirect(route('login'));
    }

    public function test_user_can_view_detail(): void
    {
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($this->admin)->get(route('pengguna.show', $user));

        $response->assertOk();
        $response->assertSee($user->name);
    }

    public function test_additional_roles_are_synced_on_update(): void
    {
        $user = User::factory()->create(['role' => 'guru']);
        UserRole::create(['user_id' => $user->id, 'role' => 'tata_usaha']);

        $response = $this->actingAs($this->admin)->put(route('pengguna.update', $user), $this->payload([
            'additional_roles' => ['wakamad_kurikulum'],
        ]));

        $response->assertRedirect();
        $this->assertDatabaseHas('user_roles', ['user_id' => $user->id, 'role' => 'wakamad_kurikulum']);
        $this->assertDatabaseMissing('user_roles', ['user_id' => $user->id, 'role' => 'tata_usaha']);
    }
}
