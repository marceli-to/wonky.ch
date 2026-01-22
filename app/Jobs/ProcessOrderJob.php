<?php

namespace App\Jobs;

use App\Actions\Order\GenerateInvoicePdf;
use App\Models\Order;
use App\Notifications\Order\ConfirmationNotification;
use App\Notifications\Order\InformationNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class ProcessOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public Order $order
    ) {}

    public function handle(): void
    {
        // Generate invoice PDF
        $invoicePath = (new GenerateInvoicePdf)->execute($this->order);

        // Send confirmation email to customer (with PDF attachment)
        Notification::route('mail', $this->order->invoice_email)
            ->notify(new ConfirmationNotification($this->order, $invoicePath));

        // Send information email to admin (with PDF attachment)
        Notification::route('mail', config('mail.to'))
            ->notify(new InformationNotification($this->order, $invoicePath));
    }
}
