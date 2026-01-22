<div>

  @if(empty($cart['items']))
    <div class="text-center py-48">
      <i class="ph ph-shopping-cart text-5xl text-gray-300 mb-16"></i>
      <p class="text-gray-500">Ihr Warenkorb ist leer</p>
      <a href="{{ route('page.products') }}" class="inline-block mt-24 text-gray-900 underline hover:no-underline">
        Produkte entdecken
      </a>
    </div>
  @else
    <div class="space-y-32">

      <!-- Cart Items -->
      <div class="border border-gray-200 rounded-lg divide-y divide-gray-200">
        @foreach($cart['items'] as $item)
          @php $cartKey = $item['cart_key'] ?? $item['uuid']; @endphp

          <div class="p-16" wire:key="cart-item-{{ $cartKey }}">
            <div class="flex gap-16">

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

              <!-- Details -->
              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-8">
                  <div>
                    <h3 class="font-medium text-gray-900">{{ $item['title'] }}</h3>
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

                <div class="mt-12 flex items-center justify-between">
                  <livewire:cart.button
                    :productUuid="$item['uuid']"
                    :cartKey="$cartKey"
                    :showButton="false"
                    :key="'cart-page-button-' . $cartKey" />
                  <span class="font-semibold text-gray-900">
                    <x-cart.money :amount="$item['price'] * $item['quantity']" />
                  </span>
                </div>
              </div>

            </div>
          </div>
        @endforeach
      </div>

      <!-- Totals -->
      <div class="border border-gray-200 rounded-lg p-16 space-y-12">
        <div class="flex justify-between text-gray-600">
          <span>Zwischensumme</span>
          <x-cart.money :amount="$cart['subtotal'] ?? $cart['total']" />
        </div>
        <div class="flex justify-between text-gray-600">
          <span>MwSt. ({{ config('invoice.tax_rate', 8.1) }}%)</span>
          <x-cart.money :amount="$cart['tax'] ?? 0" />
        </div>
        <div class="flex justify-between text-lg font-semibold text-gray-900 pt-12 border-t border-gray-200">
          <span>Total</span>
          <x-cart.money :amount="$cart['total']" />
        </div>
      </div>

      <!-- Continue Button -->
      <a
        href="{{ route('page.checkout.invoice-address') }}"
        class="block w-full py-12 px-24 bg-gray-900 text-white text-center rounded-lg font-medium hover:bg-gray-800 transition-colors"
      >
        Weiter zur Kasse
      </a>

    </div>
  @endif

</div>
