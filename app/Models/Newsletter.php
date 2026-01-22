<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Newsletter extends Model
{
    protected $fillable = [
        'uuid',
        'subject',
        'preheader',
        'recipients_count',
        'sent_count',
        'sent_at',
    ];

    protected $casts = [
        'recipients_count' => 'integer',
        'sent_count' => 'integer',
        'sent_at' => 'datetime',
    ];

    public function isSent(): bool
    {
        return $this->sent_at !== null;
    }

    public function isSending(): bool
    {
        return $this->recipients_count > 0 && $this->sent_count < $this->recipients_count;
    }

    public function getProgressAttribute(): string
    {
        if ($this->recipients_count === 0) {
            return '';
        }

        return "{$this->sent_count}/{$this->recipients_count}";
    }

    public function articles(): HasMany
    {
        return $this->hasMany(NewsletterArticle::class)->orderBy('order');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($newsletter) {
            if (empty($newsletter->uuid)) {
                $newsletter->uuid = (string) Str::uuid();
            }
        });
    }
}
