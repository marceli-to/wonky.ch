<?php

namespace App\Livewire\Checkout;

use App\Models\Order;
use Livewire\Component;

class Confirmation extends Component
{
    public ?Order $order = null;

    public function mount(): void
    {
        $orderId = session()->get('completed_order_id');

        if ($orderId) {
            $this->order = Order::with('items')->find($orderId);
        }
    }

    public function render()
    {
        return view('livewire.checkout.confirmation');
    }
}
