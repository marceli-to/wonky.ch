<?php

namespace App\Livewire\Cart;

use App\Actions\Cart\Destroy as DestroyCartAction;
use App\Actions\Cart\Get as GetCartAction;
use App\Actions\Cart\Update as UpdateCartAction;
use Livewire\Attributes\On;
use Livewire\Component;

class Cart extends Component
{
    public array $cart;

    public function mount(): void
    {
        $this->cart = (new GetCartAction)->execute();
        $this->calculateTotals();
    }

    #[On('cart-updated')]
    public function updateCart(): void
    {
        $this->cart = (new GetCartAction)->execute();
        $this->calculateTotals();
    }

    private function calculateTotals(): void
    {
        $taxRate = config('invoice.tax_rate') / 100;
        $items = collect($this->cart['items'] ?? []);

        $subtotal = $items->sum(fn ($item) => $item['price'] * $item['quantity']);
        $shipping = $items->sum(fn ($item) => $item['shipping_price'] ?? 0);

        $this->cart['subtotal'] = $subtotal + $shipping;
        $this->cart['shipping'] = $shipping;
        $this->cart['tax'] = $this->cart['subtotal'] * $taxRate;
        $this->cart['total'] = $this->cart['subtotal'] + $this->cart['tax'];
    }

    public function removeItem(string $cartKey): void
    {
        $this->cart = (new GetCartAction)->execute();

        $this->cart['items'] = collect($this->cart['items'])
            ->reject(fn ($item) => ($item['cart_key'] ?? $item['uuid']) === $cartKey)
            ->values()
            ->toArray();

        $this->cart['quantity'] = collect($this->cart['items'])->sum('quantity');

        if ($this->cart['quantity'] <= 0) {
            (new DestroyCartAction)->execute();
            $this->dispatch('cart-updated');
            $this->redirect(route('page.checkout.basket'));

            return;
        }

        $this->updateTotal();
        $this->dispatch('cart-updated');
    }

    public function updateQuantity(string $cartKey, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeItem($cartKey);

            return;
        }

        $this->cart = (new GetCartAction)->execute();

        $this->cart['items'] = collect($this->cart['items'])
            ->map(function ($item) use ($cartKey, $quantity) {
                if (($item['cart_key'] ?? $item['uuid']) === $cartKey) {
                    $item['quantity'] = $quantity;
                }

                return $item;
            })
            ->toArray();

        $this->updateTotal();
        $this->dispatch('cart-updated');
    }

    public function updateShipping(string $cartKey, int $shippingMethodId): void
    {
        $this->cart = (new GetCartAction)->execute();

        $this->cart['items'] = collect($this->cart['items'])
            ->map(function ($item) use ($cartKey, $shippingMethodId) {
                if (($item['cart_key'] ?? $item['uuid']) === $cartKey) {
                    $item['selected_shipping'] = $shippingMethodId;
                    $method = collect($item['shipping_methods'] ?? [])->firstWhere('id', $shippingMethodId);
                    $item['shipping_price'] = $method['price'] ?? 0;
                    $item['shipping_name'] = $method['name'] ?? 'Versand';
                }

                return $item;
            })
            ->toArray();

        $this->updateTotal();
        $this->dispatch('cart-updated');
    }

    private function updateTotal(): void
    {
        $taxRate = config('invoice.tax_rate') / 100;
        $items = collect($this->cart['items']);

        $subtotal = $items->sum(fn ($item) => $item['price'] * $item['quantity']);
        $shipping = $items->sum(fn ($item) => $item['shipping_price'] ?? 0);

        $this->cart['subtotal'] = $subtotal + $shipping;
        $this->cart['shipping'] = $shipping;
        $this->cart['tax'] = $this->cart['subtotal'] * $taxRate;
        $this->cart['total'] = $this->cart['subtotal'] + $this->cart['tax'];
        $this->cart['quantity'] = $items->sum('quantity');

        (new UpdateCartAction)->execute($this->cart);
    }

    public function render()
    {
        return view('livewire.cart.cart');
    }
}
