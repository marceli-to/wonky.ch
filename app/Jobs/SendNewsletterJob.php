<?php

namespace App\Jobs;

use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public Newsletter $newsletter,
        public Subscriber $subscriber
    ) {}

    public function handle(): void
    {
        Mail::to($this->subscriber->email)
            ->send(new NewsletterMail(
                $this->newsletter,
                isPreview: false,
                unsubscribeToken: $this->subscriber->token
            ));

        $this->newsletter->increment('sent_count');
    }
}
