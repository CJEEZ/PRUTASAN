<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CommunicationDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_header_has_notification_bell_and_unread_indicator(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Start Selling');
        $response->assertSee('aria-label="Notifications"');
        $response->assertSee('id="header-notification-panel"');
    }

    public function test_admin_header_has_notification_bell_and_unread_badge(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        \App\Models\Notification::create([
            'user_id' => $admin->id,
            'type' => 'order_update',
            'title' => 'New order activity',
            'message' => 'A customer placed a fresh order.',
            'is_read' => false,
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('aria-label="Notifications"');
        $response->assertSee('id="admin-notification-toggle"');
        $response->assertSee('id="admin-notification-badge"');
        $response->assertSee('Mark all read');
    }

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

    public function test_seller_messages_page_uses_uploaded_profile_photo(): void
    {
        $user = User::factory()->create([
            'role' => 'seller',
            'name' => 'Seller Photo User',
            'profile_photo_path' => 'profile_photos/seller-profile.png',
        ]);

        $response = $this->actingAs($user)->get('/seller/messages');

        $response->assertStatus(200);
        $response->assertSee('storage/profile_photos/seller-profile.png');
    }

    public function test_admin_dashboard_has_messages_category(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('/admin/messages');
        $response->assertDontSee('Send a message and review recent communications');
    }

    public function test_admin_profile_page_loads_with_admin_specific_content(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'System Admin',
            'email' => 'admin.profile@example.com',
        ]);

        $response = $this->actingAs($admin)->get('/admin/profile');

        $response->assertStatus(200);
        $response->assertSee('System Admin');
        $response->assertSee('Admin Profile');
        $response->assertSee('Platform Overview');
        $response->assertSee('Change password');
    }

    public function test_admin_profile_can_upload_profile_photo(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'System Admin',
            'email' => 'admin.photo@example.com',
        ]);

        $response = $this->actingAs($admin)->post('/admin/profile', [
            'name' => 'System Admin',
            'email' => 'admin.photo@example.com',
            'profile_photo' => UploadedFile::fake()->create('admin-avatar.png', 100, 'image/png'),
        ]);

        $response->assertRedirect(route('admin.profile'));
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'email' => 'admin.photo@example.com',
        ]);
        $admin->refresh();
        $this->assertNotNull($admin->profile_photo_path);
    }

    public function test_admin_messages_page_lists_newly_created_registered_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin User', 'email' => 'admin@example.com']);
        $newCustomer = User::factory()->create(['role' => 'customer', 'name' => 'New Customer', 'email' => 'newcustomer@example.com']);

        $response = $this->actingAs($admin)->get('/admin/messages');

        $response->assertStatus(200);
        $response->assertSee('New Customer');
        $response->assertSee('customer');
    }

    public function test_admin_delete_conversation_removes_only_the_thread_not_the_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin User', 'email' => 'admin@example.com']);
        $customer = User::factory()->create(['role' => 'customer', 'name' => 'Delete Me Customer', 'email' => 'delete-me@example.com']);

        $conversation = Conversation::create([
            'user_id_1' => $admin->id,
            'user_id_2' => $customer->id,
            'thread_key' => 'users:' . min($admin->id, $customer->id) . ':' . max($admin->id, $customer->id),
            'subject' => 'Customer support',
            'target_role' => 'customer',
            'last_message_at' => now(),
        ]);

        Inquiry::create([
            'conversation_id' => $conversation->id,
            'user_id' => $admin->id,
            'sender_id' => $admin->id,
            'recipient_id' => $customer->id,
            'name' => $admin->name,
            'email' => $admin->email,
            'recipient_email' => $customer->email,
            'subject' => 'Customer support',
            'category' => 'general',
            'target_role' => 'customer',
            'priority' => 'normal',
            'message' => 'Hello from admin',
            'is_read' => true,
            'status' => 'pending',
            'thread_key' => 'users:' . min($admin->id, $customer->id) . ':' . max($admin->id, $customer->id),
        ]);

        $this->actingAs($admin)->delete('/inquiries/conversations/' . $conversation->id)
            ->assertRedirect();

        $this->assertDatabaseMissing('conversations', ['id' => $conversation->id]);
        $this->assertDatabaseMissing('inquiries', ['conversation_id' => $conversation->id]);

        $response = $this->actingAs($admin)->get('/admin/messages');
        $response->assertStatus(200);
        $response->assertSee('Delete Me Customer');
    }

    public function test_chat_message_is_saved_for_the_recipient(): void
    {
        $sender = User::factory()->create(['role' => 'user', 'name' => 'Jane Customer', 'email' => 'jane@example.com']);
        $recipient = User::factory()->create(['role' => 'seller', 'name' => 'Seller One', 'email' => 'seller@example.com']);

        $response = $this->actingAs($sender)->post('/inquiries', [
            'message' => 'Hello seller, can you confirm my order?',
            'recipient_email' => $recipient->email,
            'recipient_name' => $recipient->name,
            'recipient_role' => 'seller',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('inquiries', [
            'user_id' => $sender->id,
            'email' => $recipient->email,
            'target_role' => 'seller',
            'message' => 'Hello seller, can you confirm my order?',
        ]);
    }

    public function test_role_based_messages_are_kept_separate_per_user(): void
    {
        $customerOne = User::factory()->create(['role' => 'customer', 'name' => 'Customer One', 'email' => 'customer1@example.com']);
        $customerTwo = User::factory()->create(['role' => 'customer', 'name' => 'Customer Two', 'email' => 'customer2@example.com']);

        $this->actingAs($customerOne)->post('/inquiries', [
            'message' => 'Support for customer one',
            'recipient_name' => 'Support team',
            'recipient_email' => 'support@fruitexpress.com',
            'recipient_role' => 'support',
        ]);

        $this->actingAs($customerTwo)->post('/inquiries', [
            'message' => 'Support for customer two',
            'recipient_name' => 'Support team',
            'recipient_email' => 'support@fruitexpress.com',
            'recipient_role' => 'support',
        ]);

        $this->assertSame(2, Conversation::where('target_role', 'support')->count());
        $this->assertSame(2, Inquiry::where('target_role', 'support')->count());
    }

    public function test_different_admin_contacts_do_not_share_the_same_role_thread(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'name' => 'Customer One', 'email' => 'customer@example.com']);

        $this->actingAs($customer)->post('/inquiries', [
            'message' => 'Message to admin alpha',
            'recipient_name' => 'Admin Alpha',
            'recipient_role' => 'admin',
        ]);

        $this->actingAs($customer)->post('/inquiries', [
            'message' => 'Message to admin beta',
            'recipient_name' => 'Admin Beta',
            'recipient_role' => 'admin',
        ]);

        $this->assertSame(2, Conversation::where('target_role', 'admin')->count());
        $this->assertSame(2, Inquiry::where('target_role', 'admin')->count());
    }
}
