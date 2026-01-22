<?php

namespace App\Actions\Cart;

class Destroy
{
    public function execute(): void
    {
        session()->forget([
            'cart',
            'invoice_address',
            'delivery_address',
            'payment_method',
            'order_step',
        ]);
    }
}
