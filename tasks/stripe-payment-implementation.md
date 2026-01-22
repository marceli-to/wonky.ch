# Stripe Payment Implementation Plan

## Overview

Implement Stripe Checkout for the shop with support for:
- Credit/Debit Cards
- Apple Pay
- Google Pay
- Twint (Swiss payment method)

## Prerequisites

### 1. Stripe Account Setup

1. Create a Stripe account at [stripe.com](https://stripe.com)
2. Enable the following payment methods in Stripe Dashboard → Settings → Payment methods:
   - Cards (enabled by default)
   - Apple Pay
   - Google Pay
   - Twint (available for Swiss merchants)
3. Obtain API keys from Dashboard → Developers → API keys:
   - Publishable key (`pk_live_...` or `pk_test_...`)
   - Secret key (`sk_live_...` or `sk_test_...`)

### 2. Apple Pay Domain Verification

1. Download the domain verification file from Stripe Dashboard
2. Place it at `public/.well-known/apple-developer-merchantid-domain-association`
3. Register your domain in Stripe Dashboard → Settings → Payment methods → Apple Pay

---

## Implementation Steps

### Step 1: Install Stripe PHP SDK

```bash
composer require stripe/stripe-php
```

### Step 2: Environment Configuration

Add to `.env`:

```env
STRIPE_PUBLIC_KEY=pk_test_xxxxx
STRIPE_SECRET_KEY=sk_test_xxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxx
```

Add to `config/services.php`:

```php
'stripe' => [
    'public_key' => env('STRIPE_PUBLIC_KEY'),
    'secret_key' => env('STRIPE_SECRET_KEY'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
],
```

### Step 3: Database Migration for Orders

Create migration for orders table:

```bash
php artisan make:migration create_orders_table
```

```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();
    $table->string('order_number')->unique();
    $table->string('stripe_checkout_session_id')->nullable();
    $table->string('stripe_payment_intent_id')->nullable();
    $table->string('payment_status')->default('pending'); // pending, paid, failed, refunded
    $table->string('payment_method')->nullable(); // card, apple_pay, google_pay, twint

    // Customer info
    $table->string('email');
    $table->string('phone')->nullable();

    // Invoice address
    $table->string('invoice_firstname');
    $table->string('invoice_lastname');
    $table->string('invoice_company')->nullable();
    $table->string('invoice_street');
    $table->string('invoice_zip');
    $table->string('invoice_city');
    $table->string('invoice_country')->default('CH');

    // Shipping address
    $table->boolean('use_invoice_address')->default(true);
    $table->string('shipping_firstname')->nullable();
    $table->string('shipping_lastname')->nullable();
    $table->string('shipping_company')->nullable();
    $table->string('shipping_street')->nullable();
    $table->string('shipping_zip')->nullable();
    $table->string('shipping_city')->nullable();
    $table->string('shipping_country')->nullable();

    // Totals
    $table->integer('subtotal'); // in cents
    $table->integer('shipping_cost')->default(0);
    $table->integer('total'); // in cents
    $table->string('currency')->default('CHF');

    $table->text('notes')->nullable();
    $table->timestamp('paid_at')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

Create pivot table for order items:

```bash
php artisan make:migration create_order_items_table
```

```php
Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_id')->constrained()->onDelete('restrict');
    $table->string('product_title');
    $table->integer('quantity');
    $table->integer('unit_price'); // in cents
    $table->integer('total_price'); // in cents
    $table->timestamps();
});
```

### Step 4: Create Order Model

```bash
php artisan make:model Order
```

```php
// app/Models/Order.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'order_number',
        'stripe_checkout_session_id',
        'stripe_payment_intent_id',
        'payment_status',
        'payment_method',
        'email',
        'phone',
        'invoice_firstname',
        'invoice_lastname',
        'invoice_company',
        'invoice_street',
        'invoice_zip',
        'invoice_city',
        'invoice_country',
        'use_invoice_address',
        'shipping_firstname',
        'shipping_lastname',
        'shipping_company',
        'shipping_street',
        'shipping_zip',
        'shipping_city',
        'shipping_country',
        'subtotal',
        'shipping_cost',
        'total',
        'currency',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'use_invoice_address' => 'boolean',
        'paid_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->uuid = (string) Str::uuid();
            $order->order_number = self::generateOrderNumber();
        });
    }

    public static function generateOrderNumber(): string
    {
        $lastOrder = self::withTrashed()->latest('id')->first();
        $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
        return 'WK-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total / 100, 2) . ' ' . $this->currency;
    }
}
```

### Step 5: Create Stripe Service

```bash
mkdir -p app/Services
```

```php
// app/Services/StripeService.php
namespace App\Services;

use App\Models\Order;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret_key'));
    }

    public function createCheckoutSession(Order $order, array $cartItems): Session
    {
        $lineItems = [];

        foreach ($cartItems as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower($order->currency),
                    'product_data' => [
                        'name' => $item['title'],
                    ],
                    'unit_amount' => $item['price'], // price in cents
                ],
                'quantity' => $item['quantity'],
            ];
        }

        // Add shipping if applicable
        if ($order->shipping_cost > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower($order->currency),
                    'product_data' => [
                        'name' => 'Versandkosten',
                    ],
                    'unit_amount' => $order->shipping_cost,
                ],
                'quantity' => 1,
            ];
        }

        return Session::create([
            'payment_method_types' => [
                'card',
                'twint',
            ],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success', ['order' => $order->uuid]),
            'cancel_url' => route('checkout.cancel', ['order' => $order->uuid]),
            'customer_email' => $order->email,
            'metadata' => [
                'order_id' => $order->id,
                'order_uuid' => $order->uuid,
            ],
            'payment_intent_data' => [
                'metadata' => [
                    'order_id' => $order->id,
                    'order_uuid' => $order->uuid,
                ],
            ],
        ]);
    }

    public function constructWebhookEvent(string $payload, string $signature): \Stripe\Event
    {
        return Webhook::constructEvent(
            $payload,
            $signature,
            config('services.stripe.webhook_secret')
        );
    }
}
```

### Step 6: Create Checkout Controller

```php
// app/Http/Controllers/CheckoutController.php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\StripeService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private StripeService $stripeService
    ) {}

    public function index()
    {
        $cart = session('cart', []);

        if (empty($cart['items'])) {
            return redirect()->route('cart.index')
                ->with('error', 'Ihr Warenkorb ist leer.');
        }

        return view('checkout.index', compact('cart'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'invoice_firstname' => 'required|string|max:255',
            'invoice_lastname' => 'required|string|max:255',
            'invoice_company' => 'nullable|string|max:255',
            'invoice_street' => 'required|string|max:255',
            'invoice_zip' => 'required|string|max:20',
            'invoice_city' => 'required|string|max:255',
            'invoice_country' => 'required|string|size:2',
            'use_invoice_address' => 'boolean',
            'shipping_firstname' => 'nullable|required_if:use_invoice_address,false|string|max:255',
            'shipping_lastname' => 'nullable|required_if:use_invoice_address,false|string|max:255',
            'shipping_street' => 'nullable|required_if:use_invoice_address,false|string|max:255',
            'shipping_zip' => 'nullable|required_if:use_invoice_address,false|string|max:20',
            'shipping_city' => 'nullable|required_if:use_invoice_address,false|string|max:255',
            'shipping_country' => 'nullable|required_if:use_invoice_address,false|string|size:2',
            'notes' => 'nullable|string',
        ]);

        $cart = session('cart');

        // Calculate totals
        $subtotal = collect($cart['items'])->sum(fn($item) => $item['price'] * $item['quantity']);
        $shippingCost = 900; // 9.00 CHF in cents
        $total = $subtotal + $shippingCost;

        // Create order
        $order = Order::create([
            ...$validated,
            'use_invoice_address' => $validated['use_invoice_address'] ?? true,
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total' => $total,
            'currency' => 'CHF',
        ]);

        // Create order items
        foreach ($cart['items'] as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_title' => $item['title'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'total_price' => $item['price'] * $item['quantity'],
            ]);
        }

        // Create Stripe Checkout Session
        $session = $this->stripeService->createCheckoutSession($order, $cart['items']);

        // Store session ID
        $order->update(['stripe_checkout_session_id' => $session->id]);

        return redirect($session->url);
    }

    public function success(Request $request, string $order)
    {
        $order = Order::where('uuid', $order)->firstOrFail();

        // Clear cart after successful payment
        session()->forget('cart');

        return view('checkout.success', compact('order'));
    }

    public function cancel(Request $request, string $order)
    {
        $order = Order::where('uuid', $order)->firstOrFail();

        return view('checkout.cancel', compact('order'));
    }
}
```

### Step 7: Create Webhook Controller

```php
// app/Http/Controllers/StripeWebhookController.php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function __construct(
        private StripeService $stripeService
    ) {}

    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $event = $this->stripeService->constructWebhookEvent($payload, $signature);
        } catch (\Exception $e) {
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event->data->object);
                break;

            default:
                Log::info('Unhandled Stripe event', ['type' => $event->type]);
        }

        return response()->json(['status' => 'success']);
    }

    private function handleCheckoutSessionCompleted($session)
    {
        $order = Order::where('stripe_checkout_session_id', $session->id)->first();

        if (!$order) {
            Log::warning('Order not found for checkout session', [
                'session_id' => $session->id,
            ]);
            return;
        }

        $order->update([
            'stripe_payment_intent_id' => $session->payment_intent,
            'payment_status' => 'paid',
            'payment_method' => $session->payment_method_types[0] ?? 'unknown',
            'paid_at' => now(),
        ]);

        // Reduce stock
        foreach ($order->items as $item) {
            $item->product->decrement('stock', $item->quantity);
        }

        // Send order confirmation email (implement as needed)
        // $order->notify(new OrderConfirmationNotification());

        Log::info('Order payment completed', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ]);
    }

    private function handlePaymentIntentSucceeded($paymentIntent)
    {
        Log::info('Payment intent succeeded', [
            'payment_intent_id' => $paymentIntent->id,
        ]);
    }

    private function handlePaymentIntentFailed($paymentIntent)
    {
        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($order) {
            $order->update(['payment_status' => 'failed']);
        }

        Log::warning('Payment intent failed', [
            'payment_intent_id' => $paymentIntent->id,
        ]);
    }
}
```

### Step 8: Define Routes

Add to `routes/web.php`:

```php
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeWebhookController;

// Checkout routes
Route::middleware(['web'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel/{order}', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
});

// Stripe webhook (exclude CSRF)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook')
    ->withoutMiddleware(['web', \App\Http\Middleware\VerifyCsrfToken::class]);
```

### Step 9: Disable CSRF for Webhook

Add webhook route to CSRF exception in `app/Http/Middleware/VerifyCsrfToken.php`:

```php
protected $except = [
    'stripe/webhook',
];
```

Or for Laravel 11+, in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->validateCsrfTokens(except: [
        'stripe/webhook',
    ]);
})
```

### Step 10: Create Filament Resource for Orders

```bash
php artisan make:filament-resource Order --generate
```

Customize the resource to display order details, payment status, and allow status updates.

---

## Testing

### Local Testing with Stripe CLI

1. Install Stripe CLI: https://stripe.com/docs/stripe-cli
2. Login: `stripe login`
3. Forward webhooks: `stripe listen --forward-to localhost:8000/stripe/webhook`
4. Use test cards:
   - Success: `4242 4242 4242 4242`
   - Decline: `4000 0000 0000 0002`
   - 3D Secure: `4000 0000 0000 3220`

### Test Twint

In test mode, Twint can be tested using the Stripe test environment. The flow will simulate the Twint payment process.

---

## Payment Method Configuration Notes

### Apple Pay & Google Pay

These are automatically enabled when you enable "Cards" in Stripe Checkout. They appear based on:
- **Apple Pay**: Customer uses Safari on macOS/iOS with Apple Pay configured
- **Google Pay**: Customer uses Chrome with Google Pay configured

No additional code changes needed - Stripe Checkout handles this automatically.

### Twint

Twint is available for Swiss merchants (CHF currency). Enable it in:
Stripe Dashboard → Settings → Payment methods → Twint

---

## Security Checklist

- [ ] Never log or expose Stripe secret keys
- [ ] Always verify webhook signatures
- [ ] Use HTTPS in production
- [ ] Validate all user input server-side
- [ ] Store prices in cents to avoid floating-point issues
- [ ] Implement idempotency for webhook handlers
- [ ] Set up Stripe Dashboard alerts for failed payments

---

## Production Deployment

1. Switch to live Stripe API keys
2. Configure webhook endpoint in Stripe Dashboard
3. Verify Apple Pay domain
4. Test all payment methods with small amounts
5. Monitor Stripe Dashboard for issues
