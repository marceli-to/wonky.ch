<?php

namespace App\Http\Middleware;

use App\Actions\Cart\Get as GetCart;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCorrectOrderStep
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $requiredStep): Response
    {
        $cart = (new GetCart)->execute();
        $currentStep = $cart['order_step'] ?? 1;

        if ($currentStep < $requiredStep) {
            return redirect()->route($this->getRedirectRoute($currentStep))->with('error', 'Please complete the previous steps before proceeding.');
        }

        return $next($request);
    }

    private function getRedirectRoute($step)
    {
        $routes = [
            1 => 'page.checkout.basket',
            2 => 'page.checkout.delivery-address',
            3 => 'page.checkout.payment',
            4 => 'page.checkout.summary',
        ];

        return $routes[$step] ?? 'page.checkout.basket';
    }
}
