<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class ShippingMethod extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'price',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Products that have this shipping method.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_shipping_method')
            ->withPivot('price')
            ->withTimestamps();
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($shippingMethod) {
            if (empty($shippingMethod->uuid)) {
                $shippingMethod->uuid = (string) Str::uuid();
            }
        });
    }
}
