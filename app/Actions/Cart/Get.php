<?php

namespace App\Actions\Cart;

class Get
{
    public function execute(): array
    {
        return session()->get('cart', [
            'items' => [],
            'quantity' => 0,
            'total' => 0,
        ]);
    }
}
