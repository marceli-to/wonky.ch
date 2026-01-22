<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Notifications\Order\ConfirmationNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class PreviewOrderMail extends Command
{
    protected $signature = 'mail:preview
                            {order : The order ID or UUID}
                            {--to= : Email address to send to (defaults to order email)}';

    protected $description = 'Send a test order confirmation email';

    public function handle(): int
    {
        $identifier = $this->argument('order');

        $order = Order::with('items')
            ->where('id', $identifier)
            ->orWhere('uuid', $identifier)
            ->first();

        if (! $order) {
            $this->error("Order not found: {$identifier}");

            return Command::FAILURE;
        }

        $email = $this->option('to') ?: $order->invoice_email;

        $this->info("Sending test email for order: {$order->order_number}");
        $this->info("To: {$email}");

        Notification::route('mail', $email)
            ->notify(new ConfirmationNotification($order, null));

        $this->info('Email sent!');

        return Command::SUCCESS;
    }
}
