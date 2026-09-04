<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryThreadingTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_messages_form_includes_recipient_id_for_selected_contact(): void
    {
        $user = User::factory()->create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'role' => 'customer',
        ]);

        $contact = User::factory()->create([
            'name' => 'Seller User',
            'email' => 'seller@example.com',
            'role' => 'seller',
        ]);

        $this->actingAs($user)
            ->get('/profile/notifications')
            ->assertOk()
            ->assertSee('name="recipient_id"', false)
            ->assertSee('data-user-id="' . $contact->id . '"', false);
    }

    public function test_user_to_user_messages_reuse_the_real_pair_and_rewrite_stale_role_conversations(): void
    {
        $sellerOne = User::factory()->create([
            'name' => 'Seller One',
            'email' => 'seller1@example.com',
            'role' => 'seller',
        ]);

        $sellerTwo = User::factory()->create([
            'name' => 'Seller Two',
            'email' => 'seller2@example.com',
            'role' => 'seller',
        ]);

        $staleConversation = Conversation::create([
            'user_id_1' => $sellerOne->id,
            'user_id_2' => null,
            'thread_key' => 'role:' . $sellerOne->id . ':seller',
            'subject' => 'Old seller thread',
            'target_role' => 'seller',
            'last_message_at' => now(),
        ]);

        $this->actingAs($sellerOne)
            ->post('/inquiries', [
                'recipient_id' => $sellerTwo->id,
                'recipient_email' => $sellerTwo->email,
                'recipient_role' => 'seller',
                'message' => 'Hello there',
                'subject' => 'New thread',
            ])
            ->assertRedirect();

        $expectedThreadKey = 'users:' . min($sellerOne->id, $sellerTwo->id) . ':' . max($sellerOne->id, $sellerTwo->id);

        $this->assertDatabaseHas('conversations', [
            'id' => $staleConversation->id,
            'thread_key' => $expectedThreadKey,
            'target_role' => 'seller',
        ]);

        $this->assertDatabaseMissing('conversations', [
            'thread_key' => 'role:' . $sellerOne->id . ':seller',
        ]);

        $this->assertSame(1, Conversation::where('thread_key', $expectedThreadKey)->count());
    }
}
