<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Notifications\OrderConfirmationNotification;
use App\Services\Pdf\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CreateInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an invoice as PDF';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $order = Order::with('products')->latest()->first();
        $invoice = (new Pdf)->create([
            'data' => $order,
            'view' => 'invoice',
            'name' => config('invoice.invoice_prefix').$order->uuid,
        ]);

        try {
            Notification::route('mail', env('MAIL_TO'))
                ->notify(
                    new OrderConfirmationNotification($order, $invoice)
                );
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
        $this->info('The command was successful!');
    }
}
