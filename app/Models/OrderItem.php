<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_name',
        'product_label',
        'product_description',
        'product_price',
        'quantity',
        'shipping_name',
        'shipping_price',
    ];

    protected $casts = [
        'product_price' => 'decimal:2',
        'shipping_price' => 'decimal:2',
    ];

    /**
     * Get the order that owns the item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the subtotal for this item.
     */
    public function getSubtotalAttribute(): float
    {
        return $this->product_price * $this->quantity;
    }

    /**
     * Get the full product name including label.
     */
    public function getFullNameAttribute(): string
    {
        if ($this->product_label) {
            return $this->product_name.', '.$this->product_label;
        }

        return $this->product_name;
    }
}
