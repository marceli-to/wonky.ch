<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;

class NewsletterController extends Controller
{
    public function confirm(string $token)
    {
        $subscriber = Subscriber::where('token', $token)->firstOrFail();

        if ($subscriber->confirmed_at) {
            return view('newsletter.already-confirmed');
        }

        $subscriber->update([
            'confirmed_at' => now(),
        ]);

        return view('newsletter.confirmed');
    }

    public function unsubscribe(string $token)
    {
        $subscriber = Subscriber::where('token', $token)->firstOrFail();

        $subscriber->update([
            'unsubscribed_at' => now(),
        ]);

        return view('newsletter.unsubscribed');
    }
}
