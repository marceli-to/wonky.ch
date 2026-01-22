<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subscriber extends Model
{
    protected $fillable = [
        'uuid',
        'email',
        'name',
        'subscribed_at',
        'confirmed_at',
        'unsubscribed_at',
        'token',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    protected function isConfirmed(): Attribute
    {
        return Attribute::get(fn () => $this->confirmed_at !== null);
    }

    protected function isActive(): Attribute
    {
        return Attribute::get(fn () => $this->confirmed_at !== null && $this->unsubscribed_at === null);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscriber) {
            if (empty($subscriber->uuid)) {
                $subscriber->uuid = (string) Str::uuid();
            }
            if (empty($subscriber->token)) {
                $subscriber->token = Str::random(64);
            }
            if (empty($subscriber->subscribed_at)) {
                $subscriber->subscribed_at = now();
            }
        });
    }
}
