<?php

namespace App\Livewire\Checkout;

use App\Actions\Cart\Update as UpdateCart;
use Livewire\Component;

class Payment extends Component
{
    public string $payment_method = 'card';

    protected $rules = [
        'payment_method' => 'required|in:card,invoice',
    ];

    public function mount(): void
    {
        $method = session()->get('payment_method');

        if ($method) {
            $this->payment_method = $method;
        }
    }

    public function save(): void
    {
        $this->validate();

        session()->put('payment_method', $this->payment_method);

        (new UpdateCart)->execute(['order_step' => 4]);

        $this->redirect(route('page.checkout.summary'));
    }

    public function render()
    {
        return view('livewire.checkout.payment');
    }
}
