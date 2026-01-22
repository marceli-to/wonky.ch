<?php

namespace App\Livewire\Cart;

use App\Actions\Cart\Destroy as DestroyCartAction;
use App\Actions\Cart\Get as GetCartAction;
use App\Actions\Cart\Update as UpdateCartAction;
use Livewire\Attributes\On;
use Livewire\Component;

class MiniCart extends Component
{
    public array $cart = [];

    public bool $show = false;

    public bool $isLanding = false;

    public function mount(): void
    {
        $this->cart = (new GetCartAction)->execute();
        $this->isLanding = request()->routeIs('page.landing');
    }

    #[On('toggle-mini-cart')]
    public function toggle(): void
    {
        $this->show = ! $this->show;
    }

    #[On('cart-updated')]
    public function updateCart(): void
    {
        $this->cart = (new GetCartAction)->execute();
    }

    #[On('open-mini-cart')]
    public function open(): void
    {
        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
    }

    public function removeItem(string $cartKey): void
    {
        $this->cart = (new GetCartAction)->execute();
        $items = collect($this->cart['items'])->filter(function ($item) use ($cartKey) {
            return ($item['cart_key'] ?? $item['uuid']) !== $cartKey;
        })->values()->toArray();

        $this->cart['items'] = $items;
        $this->cart['quantity'] = collect($items)->sum('quantity');

        if ($this->cart['quantity'] <= 0) {
            (new DestroyCartAction)->execute();
            $this->dispatch('cart-updated');
            $this->redirect(route('page.checkout.basket'));

            return;
        }

        $this->updateTotal();
    }

    private function updateTotal(): void
    {
        $this->cart['total'] = collect($this->cart['items'])->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        (new UpdateCartAction)->execute($this->cart);
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('livewire.cart.mini-cart');
    }
}
