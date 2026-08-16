<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_is_redirected_to_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'clarencejohn@02@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'seller',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_can_login_and_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@fruitexpress.com',
            'password' => Hash::make('admin123456'),
            'role' => 'admin',
        ]);

        $response = $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'admin123456',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_only_canonical_admin_email_can_login_as_admin(): void
    {
        $otherAdmin = User::factory()->create([
            'email' => 'otheradmin@example.com',
            'password' => Hash::make('admin123456'),
            'role' => 'admin',
        ]);

        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => $otherAdmin->email,
            'password' => 'admin123456',
        ]);

        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors('admin');
        $this->assertGuest();

        $canonicalAdmin = User::factory()->create([
            'email' => 'admin@fruitexpress.com',
            'password' => Hash::make('admin123456'),
            'role' => 'admin',
        ]);

        $loginResponse = $this->from('/admin/login')->post('/admin/login', [
            'email' => $canonicalAdmin->email,
            'password' => 'admin123456',
        ]);

        $loginResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($canonicalAdmin);
    }
}
