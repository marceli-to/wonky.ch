<div class="relative">
  <button
    wire:click="toggleMiniCart"
    class="relative p-8 text-gray-700 hover:text-gray-900 cursor-pointer"
    aria-label="Warenkorb öffnen">

    <i class="ph ph-shopping-cart text-2xl"></i>

    @if($cartItemCount > 0)
      <span class="absolute top-0 right-0 bg-gray-900 text-white text-xs font-medium rounded-full h-20 w-20 flex items-center justify-center">
        {{ $cartItemCount > 9 ? '9+' : $cartItemCount }}
      </span>
    @endif

  </button>
</div>
