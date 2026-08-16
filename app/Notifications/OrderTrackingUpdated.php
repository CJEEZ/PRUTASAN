<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use App\Models\Order;

class OrderTrackingUpdated extends Notification
{
    use Queueable;

    protected Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        $channels = ['mail'];
        if (! empty($this->order->phone)) {
            $channels[] = 'vonage';
        }
        return $channels;
    }

    public function toMail($notifiable)
    {
        $order = $this->order;

        return (new MailMessage)
            ->subject("Order " . ($order->order_number ?? $order->id) . " - Tracking Update")
            ->greeting('Hello ' . ($order->full_name ?? 'Customer') . ',')
            ->line('Your order tracking information has been updated.')
            ->line('Status: ' . ucfirst(str_replace('_', ' ', $order->tracking_status ?? 'pending')))
            ->when(! empty($order->tracking_number), function ($mail) use ($order) {
                $mail->line('Tracking Number: ' . $order->tracking_number);
            })
            ->when(! empty($order->current_location), function ($mail) use ($order) {
                $mail->line('Current Location: ' . $order->current_location);
            })
            ->action('View Order', url(route('seller.orders.show', $order)))
            ->line('Thank you for shopping with us.');
    }

    public function toVonage($notifiable)
    {
        $order = $this->order;
        $status = ucfirst(str_replace('_', ' ', $order->tracking_status ?? 'pending'));
        $tracking = $order->tracking_number ? (" Tracking#: " . $order->tracking_number) : '';
        $location = $order->current_location ? (" Location: " . $order->current_location) : '';

        return (new VonageMessage)
            ->content("Order " . ($order->order_number ?? $order->id) . " update: " . $status . $tracking . $location);
    }
}
