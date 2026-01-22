<?php

namespace App\Http\Middleware;

use App\Actions\Cart\Get as GetCart;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrderIsPaid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cart = (new GetCart)->execute();
        if (! isset($cart['is_paid']) || $cart['is_paid'] !== true) {
            return redirect()->route('page.checkout.summary')->with('error', 'Bitte schliessen Sie den Zahlungsvorgang ab, bevor Sie fortfahren.');
        }

        return $next($request);
    }
}
