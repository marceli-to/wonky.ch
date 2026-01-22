<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret_key'));
    }

    /**
     * Create a Stripe Checkout Session for the given cart.
     *
     * @param  array  $cart  The cart data
     * @param  string  $referenceId  Unique reference for this order
     * @param  string  $customerEmail  Customer email address
     * @return Session Stripe Checkout Session
     */
    public function createCheckoutSession(array $cart, string $referenceId, string $customerEmail): Session
    {
        $lineItems = [];
        $currency = strtolower(config('services.stripe.currency', 'CHF'));

        foreach ($cart['items'] as $item) {
            // Product item
            $lineItems[] = [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => $item['title'].(! empty($item['label']) ? ' - '.$item['label'] : ''),
                    ],
                    'unit_amount' => (int) round($item['price'] * 100), // Convert to cents
                ],
                'quantity' => $item['quantity'],
            ];

            // Shipping per item (if applicable)
            if (! empty($item['shipping_price']) && $item['shipping_price'] > 0) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => 'Versand: '.$item['title'],
                        ],
                        'unit_amount' => (int) round($item['shipping_price'] * 100),
                    ],
                    'quantity' => 1,
                ];
            }
        }

        // Add tax as separate line item
        if (! empty($cart['tax']) && $cart['tax'] > 0) {
            $taxRate = config('invoice.tax_rate', 8.1);
            $lineItems[] = [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => "MwSt. {$taxRate}%",
                    ],
                    'unit_amount' => (int) round($cart['tax'] * 100),
                ],
                'quantity' => 1,
            ];
        }

        try {
            $session = Session::create([
                'payment_method_types' => [
                    'card',
                    'twint',
                ],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('payment.success', ['reference' => $referenceId]),
                'cancel_url' => route('payment.cancel', ['reference' => $referenceId]),
                'customer_email' => $customerEmail,
                'metadata' => [
                    'reference_id' => $referenceId,
                ],
                'payment_intent_data' => [
                    'metadata' => [
                        'reference_id' => $referenceId,
                    ],
                ],
            ]);

            Log::info('Stripe Checkout Session Created', [
                'session_id' => $session->id,
                'reference' => $referenceId,
            ]);

            return $session;

        } catch (\Exception $e) {
            Log::error('Stripe Checkout Session Creation Failed', [
                'message' => $e->getMessage(),
                'reference' => $referenceId,
            ]);
            throw $e;
        }
    }

    /**
     * Retrieve a Checkout Session by ID.
     */
    public function getCheckoutSession(string $sessionId): ?Session
    {
        try {
            return Session::retrieve($sessionId);
        } catch (\Exception $e) {
            Log::error('Stripe Session Fetch Failed', [
                'message' => $e->getMessage(),
                'session_id' => $sessionId,
            ]);

            return null;
        }
    }

    /**
     * Check if a payment was successful.
     */
    public function isPaymentSuccessful(string $sessionId): bool
    {
        $session = $this->getCheckoutSession($sessionId);

        if (! $session) {
            return false;
        }

        return $session->payment_status === 'paid';
    }

    /**
     * Get the payment method type from a session.
     */
    public function getPaymentMethodType(string $sessionId): ?string
    {
        $session = $this->getCheckoutSession($sessionId);

        if (! $session) {
            return null;
        }

        // Get payment method types used
        $paymentMethodTypes = $session->payment_method_types ?? [];

        // For completed payments, check the payment intent
        if ($session->payment_intent) {
            try {
                $paymentIntent = \Stripe\PaymentIntent::retrieve($session->payment_intent);
                $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentIntent->payment_method);

                return $paymentMethod->type;
            } catch (\Exception $e) {
                Log::warning('Could not retrieve payment method type', [
                    'session_id' => $sessionId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $paymentMethodTypes[0] ?? null;
    }

    /**
     * Construct and verify a webhook event.
     *
     * @throws SignatureVerificationException
     */
    public function constructWebhookEvent(string $payload, string $signature): \Stripe\Event
    {
        return Webhook::constructEvent(
            $payload,
            $signature,
            config('services.stripe.webhook_secret')
        );
    }
}
