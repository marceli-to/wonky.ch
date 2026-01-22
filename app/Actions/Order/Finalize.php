<?php

namespace App\Actions\Order;

use App\Actions\Cart\Destroy as DestroyCartAction;
use App\Jobs\ProcessOrderJob;
use App\Models\Order;

class Finalize
{
    public function execute(): void
    {
        $order = Order::find(session('completed_order_id'));

        if ($order) {
            // Dispatch job to queue (handles PDF generation and emails)
            ProcessOrderJob::dispatch($order);
        }

        // Clear cart and checkout session data
        (new DestroyCartAction)->execute();

        // Clear payment-specific session data (keep completed_order_id for confirmation page)
        session()->forget(['payment_reference', 'payment_gateway_id']);
    }
}
