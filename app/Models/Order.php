<?php

namespace App\Models;

use App\Actions\Order\GenerateOrderNumber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'order_number',
        'invoice_salutation',
        'invoice_firstname',
        'invoice_lastname',
        'invoice_street',
        'invoice_street_number',
        'invoice_zip',
        'invoice_city',
        'invoice_country',
        'invoice_email',
        'invoice_phone',
        'use_invoice_address',
        'shipping_salutation',
        'shipping_firstname',
        'shipping_lastname',
        'shipping_street',
        'shipping_street_number',
        'shipping_zip',
        'shipping_city',
        'shipping_country',
        'subtotal',
        'shipping',
        'tax',
        'total',
        'payment_method',
        'payment_reference',
        'stripe_session_id',
        'paid_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'use_invoice_address' => 'boolean',
        'paid_at' => 'datetime',
    ];

    protected $appends = [
        'invoice_name',
        'invoice_address',
        'invoice_location',
        'shipping_name',
        'shipping_address',
        'shipping_location',
    ];

    /**
     * Get the order items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Get the invoice full name.
     */
    public function getInvoiceNameAttribute(): string
    {
        return trim($this->invoice_firstname.' '.$this->invoice_lastname);
    }

    /**
     * Get the formatted invoice address.
     */
    public function getInvoiceAddressAttribute(): string
    {
        return $this->invoice_street.' '.$this->invoice_street_number;
    }

    /**
     * Get the formatted invoice location.
     */
    public function getInvoiceLocationAttribute(): string
    {
        return $this->invoice_zip.' '.$this->invoice_city;
    }

    /**
     * Get the shipping full name.
     */
    public function getShippingNameAttribute(): ?string
    {
        if ($this->use_invoice_address) {
            return $this->invoice_name;
        }

        return trim($this->shipping_firstname.' '.$this->shipping_lastname);
    }

    /**
     * Get the formatted shipping address.
     */
    public function getShippingAddressAttribute(): ?string
    {
        if ($this->use_invoice_address) {
            return $this->invoice_address;
        }

        return $this->shipping_street.' '.$this->shipping_street_number;
    }

    /**
     * Get the formatted shipping location.
     */
    public function getShippingLocationAttribute(): ?string
    {
        if ($this->use_invoice_address) {
            return $this->invoice_location;
        }

        return $this->shipping_zip.' '.$this->shipping_city;
    }

    /**
     * Check if order is paid.
     */
    public function isPaid(): bool
    {
        return ! is_null($this->paid_at);
    }

    /**
     * Create an order from session data.
     *
     * @param  array  $cart  Cart data from session
     * @param  array  $invoiceAddress  Invoice address from session
     * @param  array  $deliveryAddress  Delivery address from session (empty if same as invoice)
     * @param  string  $paymentMethod  Payment method (creditcard, invoice)
     * @param  string|null  $paymentReference  Payment reference ID
     */
    public static function createFromSession(
        array $cart,
        array $invoiceAddress,
        array $deliveryAddress,
        string $paymentMethod,
        ?string $paymentReference = null
    ): self {
        $useInvoiceAddress = empty($deliveryAddress);

        $order = self::create([
            'invoice_salutation' => ! empty($invoiceAddress['salutation']) ? $invoiceAddress['salutation'] : null,
            'invoice_firstname' => $invoiceAddress['firstname'],
            'invoice_lastname' => $invoiceAddress['lastname'],
            'invoice_street' => $invoiceAddress['street'],
            'invoice_street_number' => $invoiceAddress['street_number'] ?? '',
            'invoice_zip' => $invoiceAddress['zip'],
            'invoice_city' => $invoiceAddress['city'],
            'invoice_country' => $invoiceAddress['country'],
            'invoice_email' => $invoiceAddress['email'],
            'invoice_phone' => ! empty($invoiceAddress['phone']) ? $invoiceAddress['phone'] : null,
            'use_invoice_address' => $useInvoiceAddress,
            'shipping_salutation' => $useInvoiceAddress ? null : (! empty($deliveryAddress['salutation']) ? $deliveryAddress['salutation'] : null),
            'shipping_firstname' => $useInvoiceAddress ? null : ($deliveryAddress['firstname'] ?? null),
            'shipping_lastname' => $useInvoiceAddress ? null : ($deliveryAddress['lastname'] ?? null),
            'shipping_street' => $useInvoiceAddress ? null : ($deliveryAddress['street'] ?? null),
            'shipping_street_number' => $useInvoiceAddress ? null : ($deliveryAddress['street_number'] ?? null),
            'shipping_zip' => $useInvoiceAddress ? null : ($deliveryAddress['zip'] ?? null),
            'shipping_city' => $useInvoiceAddress ? null : ($deliveryAddress['city'] ?? null),
            'shipping_country' => $useInvoiceAddress ? null : ($deliveryAddress['country'] ?? null),
            'subtotal' => $cart['subtotal'] ?? 0,
            'shipping' => $cart['shipping'] ?? 0,
            'tax' => $cart['tax'] ?? 0,
            'total' => $cart['total'],
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference,
            'paid_at' => $paymentMethod === 'invoice' ? null : now(),
        ]);

        foreach ($cart['items'] as $item) {
            $order->items()->create([
                'product_name' => $item['name'],
                'product_label' => $item['label'] ?? null,
                'product_description' => $item['description'] ?? null,
                'product_price' => $item['price'],
                'quantity' => $item['quantity'],
                'shipping_name' => $item['shipping_name'] ?? null,
                'shipping_price' => $item['shipping_price'] ?? 0,
            ]);
        }

        return $order->load('items');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->uuid)) {
                $order->uuid = (string) Str::uuid();
            }
        });

        static::created(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = (new GenerateOrderNumber)->execute($order);
                $order->saveQuietly();
            }
        });
    }
}
