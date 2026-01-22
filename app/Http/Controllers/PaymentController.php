<?php

namespace App\Http\Controllers;

use App\Actions\Cart\Get as GetCartAction;
use App\Actions\Order\Create as CreateOrderAction;
use App\Actions\Order\Finalize as FinalizeOrderAction;
use App\Services\StripeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private StripeService $stripeService
    ) {}

    /**
     * Handle successful payment return.
     */
    public function success(Request $request, string $reference): View|RedirectResponse
    {
        $storedReference = session()->get('payment_reference');
        $sessionId = session()->get('stripe_session_id');

        // Verify reference matches
        if ($reference !== $storedReference) {
            return redirect()->route('page.checkout.basket')->with('error', 'Ungultige Zahlungsreferenz.');
        }

        // Verify payment with Stripe
        if ($sessionId && $this->stripeService->isPaymentSuccessful($sessionId)) {
            $cart = (new GetCartAction)->execute();
            $invoiceAddress = session()->get('invoice_address', []);
            $deliveryAddress = session()->get('delivery_address', []);

            // Get payment method type (card, twint, etc.)
            $paymentMethodType = $this->stripeService->getPaymentMethodType($sessionId) ?? 'card';

            // Create order in database
            (new CreateOrderAction)->execute(
                $cart,
                $invoiceAddress,
                $deliveryAddress,
                $paymentMethodType,
                $reference
            );

            // Finalize order (send emails, clear cart, etc.)
            (new FinalizeOrderAction)->execute();

            return redirect()->route('page.checkout.confirmation');
        }

        // Payment not confirmed yet - could be pending
        return view('pages.checkout.pending', [
            'reference' => $reference,
        ]);
    }

    /**
     * Handle cancelled/failed payment return.
     */
    public function cancel(Request $request, string $reference): RedirectResponse
    {
        // Clear payment session data but keep cart
        session()->forget(['payment_reference', 'stripe_session_id']);

        return redirect()->route('page.checkout.summary')->with('error', 'Die Zahlung wurde abgebrochen. Bitte versuchen Sie es erneut.');
    }

    /**
     * Handle Stripe webhook notifications.
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        if (! $signature) {
            return response()->json(['error' => 'No signature'], 400);
        }

        try {
            $event = $this->stripeService->constructWebhookEvent($payload, $signature);
        } catch (\Exception $e) {
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Log webhook for debugging
        Log::info('Stripe Webhook Received', [
            'type' => $event->type,
            'id' => $event->id,
        ]);

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'payment_intent.succeeded':
                Log::info('Payment intent succeeded', [
                    'payment_intent_id' => $event->data->object->id,
                ]);
                break;

            case 'payment_intent.payment_failed':
                Log::warning('Payment intent failed', [
                    'payment_intent_id' => $event->data->object->id,
                ]);
                break;

            default:
                Log::info('Unhandled Stripe event', ['type' => $event->type]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle completed checkout session from webhook.
     */
    private function handleCheckoutSessionCompleted($session): void
    {
        $referenceId = $session->metadata->reference_id ?? null;

        if (! $referenceId) {
            Log::warning('Checkout session completed without reference ID', [
                'session_id' => $session->id,
            ]);

            return;
        }

        Log::info('Checkout session completed via webhook', [
            'session_id' => $session->id,
            'reference_id' => $referenceId,
            'payment_status' => $session->payment_status,
        ]);

        // Note: Order creation is handled in the success callback
        // This webhook is for backup/verification purposes
    }
}
