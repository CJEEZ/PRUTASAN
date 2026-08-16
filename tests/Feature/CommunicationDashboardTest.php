<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunicationDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_profile_shows_messages_section(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/profile/notifications');

        $response->assertStatus(200);
        $response->assertSee('Messages');
        $response->assertSee('Contact support and track your conversations');
        $response->assertDontSee('Send a message to the farm team');
    }

    public function test_seller_messages_page_shows_messages_section(): void
    {
        $user = User::factory()->create(['role' => 'seller']);

        $response = $this->actingAs($user)->get('/seller/messages');

        $response->assertStatus(200);
        $response->assertSee('Messages');
        $response->assertSee('Recent messages');
        $response->assertSee('Send a message');
    }

    public function test_admin_dashboard_has_messages_category(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('/admin/messages');
        $response->assertDontSee('Send a message and review recent communications');
    }
}
