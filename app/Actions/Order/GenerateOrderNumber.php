<?php

namespace App\Actions\Order;

use App\Models\Order;

class GenerateOrderNumber
{
    /**
     * Generate order number from order ID.
     * Format: YYNNNNN (e.g., 2500001 for order ID 1 in 2025)
     */
    public function execute(Order $order): string
    {
        $year = $order->created_at->format('y');
        $paddedId = str_pad($order->id, 5, '0', STR_PAD_LEFT);

        return $year.$paddedId;
    }
}
