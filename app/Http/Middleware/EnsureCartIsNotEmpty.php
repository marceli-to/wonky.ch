<?php

namespace App\Http\Middleware;

use App\Actions\Cart\Get as GetCart;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCartIsNotEmpty
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cart = (new GetCart)->execute();
        if (! isset($cart['items']) || empty($cart['items'])) {
            return redirect()->route('page.checkout.basket');
        }

        return $next($request);
    }
}
