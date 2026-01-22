<?php

namespace App\Actions\Cart;

class Update
{
    public function execute(array $updates): array
    {
        $cart = (new Get)->execute();

        $cart = array_merge($cart, $updates);

        (new Store)->execute($cart);

        return $cart;
    }
}
