<?php

namespace App\Actions\Order;

use App\Models\Order;

class Create
{
    public function execute(
        array $cart,
        array $invoiceAddress,
        array $deliveryAddress,
        string $paymentMethod,
        ?string $paymentReference = null
    ): Order {
        $order = Order::createFromSession(
            $cart,
            $invoiceAddress,
            $deliveryAddress,
            $paymentMethod,
            $paymentReference
        );

        // Store order ID in session for confirmation page
        session()->put('completed_order_id', $order->id);

        return $order;
    }
}
