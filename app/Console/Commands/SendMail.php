<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Notifications\OrderConfirmationNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification emails to users';

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
        try {
            Notification::route('mail', env('MAIL_TO'))
                ->notify(
                    new OrderConfirmationNotification($order)
                );
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }

        $this->info('The command was successful!');
    }
}
