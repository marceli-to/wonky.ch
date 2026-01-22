<?php

namespace App\Livewire;

use App\Mail\NewsletterConfirmationMail;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;

class NewsletterSignup extends Component
{
    public string $email = '';

    public string $name = '';

    public bool $success = false;

    public ?string $error = null;

    protected $rules = [
        'email' => 'required|email|unique:subscribers,email',
        'name' => 'nullable|string|max:255',
    ];

    protected $messages = [
        'email.required' => 'Bitte geben Sie eine E-Mail-Adresse ein.',
        'email.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
        'email.unique' => 'Diese E-Mail-Adresse ist bereits angemeldet.',
    ];

    public function subscribe()
    {
        $this->validate();

        try {
            $subscriber = Subscriber::create([
                'email' => $this->email,
                'name' => $this->name ?: null,
                'subscribed_at' => now(),
                'token' => Str::random(64),
            ]);

            Mail::to($subscriber->email)->send(new NewsletterConfirmationMail($subscriber));

            $this->success = true;
            $this->reset(['email', 'name', 'error']);
        } catch (\Exception $e) {
            $this->error = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.';
        }
    }

    public function render()
    {
        return view('livewire.newsletter.signup');
    }
}
