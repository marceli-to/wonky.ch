<?php

namespace App\Console\Commands;

use App\Jobs\ProcessOrderJob;
use App\Models\Order;
use Illuminate\Console\Command;

class ProcessOrder extends Command
{
    protected $signature = 'order:process
                            {order : The order ID or UUID}
                            {--sync : Run immediately instead of queuing}';

    protected $description = 'Process an order (generate PDF and send emails)';

    public function handle(): int
    {
        $identifier = $this->argument('order');

        $order = Order::where('id', $identifier)
            ->orWhere('uuid', $identifier)
            ->first();

        if (! $order) {
            $this->error("Order not found: {$identifier}");

            return Command::FAILURE;
        }

        $this->info("Processing order: {$order->order_number}");

        if ($this->option('sync')) {
            ProcessOrderJob::dispatchSync($order);
            $this->info('Order processed immediately.');
        } else {
            ProcessOrderJob::dispatch($order);
            $this->info('Order job dispatched to queue.');
        }

        return Command::SUCCESS;
    }
}
