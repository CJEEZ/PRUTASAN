<?php

namespace App\Http\Livewire;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderTrackingModal extends Component
{
    public ?Order $order = null;
    public bool $showModal = false;
    public int|null $orderId = null;

    protected $listeners = [
        'openOrderDetails' => 'open',
    ];

    public function open(int $orderId)
    {
        $order = Order::with(['items.product', 'shipment'])->find($orderId);
        if (! $order || $order->user_id !== Auth::id()) {
            $this->dispatchBrowserEvent('flash', [
                'type' => 'error',
                'message' => 'Unable to open this order.',
            ]);
            return;
        }

        $this->order = $order;
        $this->orderId = $orderId;
        $this->showModal = true;
    }

    public function close()
    {
        $this->showModal = false;
    }

    public function refreshOrder()
    {
        if ($this->orderId) {
            $this->order = Order::with(['items.product', 'shipment'])->find($this->orderId);
        }
    }

    public function render()
    {
        return view('livewire.order-tracking-modal');
    }
}
