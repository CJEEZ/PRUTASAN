<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user
        $user = User::first();

        if ($user) {
            // Create sample order update notification
            Notification::create([
                'user_id' => $user->id,
                'type' => 'order_update',
                'title' => 'Parcel Delivered',
                'message' => 'Parcel PH2680650602203 for your order 2601261SD6XNDA has been delivered.',
                'image_url' => 'https://via.placeholder.com/100',
                'order_id' => null,
                'is_read' => false,
            ]);

            // Create sample promotion notification
            Notification::create([
                'user_id' => $user->id,
                'type' => 'promotion',
                'title' => 'Special Offer!',
                'message' => 'Get 30% off on all fresh fruits this week. Limited time offer!',
                'image_url' => 'https://via.placeholder.com/100',
                'order_id' => null,
                'is_read' => false,
            ]);

            // Create sample wallet update notification
            Notification::create([
                'user_id' => $user->id,
                'type' => 'wallet_update',
                'title' => 'Wallet Credit',
                'message' => 'You have received ₱500 credit in your wallet from your recent refund.',
                'image_url' => null,
                'order_id' => null,
                'is_read' => true,
            ]);

            // Create sample system update notification
            Notification::create([
                'user_id' => $user->id,
                'type' => 'system_update',
                'title' => 'App Update Available',
                'message' => 'A new version of Fruit2Web is available. Update now to enjoy new features.',
                'image_url' => null,
                'order_id' => null,
                'is_read' => true,
            ]);
        }
    }
}
