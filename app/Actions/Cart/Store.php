<?php

namespace App\Actions\Cart;

class Store
{
    public function execute(array $cart): void
    {
        session()->put('cart', $cart);
    }
}
