<div
  class="fixed inset-0 z-50"
  x-data="{ show: @entangle('show') }"
  :class="{ 'pointer-events-none': !show }"
  x-show="show"
  x-transition:enter="transition ease-out duration-300"
  x-transition:enter-start="opacity-0"
  x-transition:enter-end="opacity-100"
  x-transition:leave="transition ease-in duration-200"
  x-transition:leave-start="opacity-100"
  x-transition:leave-end="opacity-0"
  style="display: none;">

  <!-- Backdrop -->
  <div
    class="absolute inset-0 bg-black/50"
    @click="show = false">
  </div>

  <!-- Cart Panel -->
  <div
    class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-xl z-10"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full">

    <div class="flex flex-col h-full">

      <!-- Header -->
      <div class="flex items-center justify-between px-24 py-16 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Warenkorb</h2>
        <button
          type="button"
          @click="show = false"
          class="p-8 text-gray-500 hover:text-gray-700 cursor-pointer"
          aria-label="Schliessen">
          <i class="ph ph-x text-xl"></i>
        </button>
      </div>

      <!-- Content -->
      <div class="flex-1 overflow-y-auto p-24">

        @if(empty($cart['items']))
          <div class="flex flex-col items-center justify-center h-full text-gray-500">
            <i class="ph ph-shopping-cart text-5xl mb-16"></i>
            <p>Ihr Warenkorb ist leer</p>
          </div>
        @else
          <div class="space-y-24">
            @foreach($cart['items'] as $item)
              @php $cartKey = $item['cart_key'] ?? $item['uuid']; @endphp

              <div class="flex gap-16" wire:key="mini-cart-item-{{ $cartKey }}">

                <!-- Image -->
                @if(!empty($item['image']))
                  <div class="w-80 h-80 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                    <img
                      src="{{ Storage::url($item['image']) }}"
                      alt="{{ $item['title'] }}"
                      class="w-full h-full object-cover"
                    />
                  </div>
                @else
                  <div class="w-80 h-80 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="ph ph-image text-2xl text-gray-300"></i>
                  </div>
                @endif

                <!-- Info -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-start justify-between">
                    <div>
                      <h3 class="font-medium text-gray-900 truncate">{{ $item['title'] }}</h3>
                      @if(!empty($item['label']))
                        <p class="text-sm text-gray-500">{{ $item['label'] }}</p>
                      @endif
                    </div>
                    <button
                      wire:click="removeItem('{{ $cartKey }}')"
                      class="p-4 text-gray-400 hover:text-gray-600 cursor-pointer"
                      aria-label="Entfernen">
                      <i class="ph ph-x"></i>
                    </button>
                  </div>

                  <div class="mt-8 flex items-center justify-between">
                    <livewire:cart.button
                      :productUuid="$item['uuid']"
                      :cartKey="$cartKey"
                      :showButton="false"
                      :key="'mini-cart-qty-' . $cartKey" />
                    <span class="font-medium text-gray-900">
                      <x-cart.money :amount="$item['price'] * $item['quantity']" />
                    </span>
                  </div>
                </div>

              </div>
            @endforeach
          </div>
        @endif

      </div>

      <!-- Footer -->
      @if(!empty($cart['items']))
        <div class="border-t border-gray-200 px-24 py-16 space-y-16">

          <div class="flex items-center justify-between text-lg font-semibold">
            <span>Total</span>
            <x-cart.money :amount="$cart['total']" />
          </div>

          <a
            href="{{ route('page.checkout.basket') }}"
            wire:click="close"
            class="block w-full py-12 px-24 bg-gray-900 text-white text-center rounded-lg font-medium hover:bg-gray-800 transition-colors">
            Zum Warenkorb
          </a>

        </div>
      @endif

    </div>

  </div>

</div>
