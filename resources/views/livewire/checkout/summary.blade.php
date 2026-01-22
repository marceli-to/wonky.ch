<div>

  @if(session('error'))
    <div class="mb-24 p-16 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">
      {{ session('error') }}
    </div>
  @endif

  @error('terms_accepted')
    <div class="mb-24 p-16 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">
      {{ $message }}
    </div>
  @enderror

  <div class="space-y-32">

    <!-- Order Items -->
    <div>
      <h2 class="text-lg font-medium text-gray-900 mb-16">Bestellübersicht</h2>

      <div class="border border-gray-200 rounded-lg divide-y divide-gray-200">
        @foreach($cart['items'] as $item)
          @php $cartKey = $item['cart_key'] ?? $item['uuid']; @endphp

          <div class="p-16" wire:key="summary-item-{{ $cartKey }}">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="font-medium text-gray-900">{{ $item['title'] }}</h3>
                @if(!empty($item['label']))
                  <p class="text-sm text-gray-500">{{ $item['label'] }}</p>
                @endif
                <p class="text-sm text-gray-500">Anzahl: {{ $item['quantity'] }}</p>
              </div>
              <span class="font-medium text-gray-900">
                <x-cart.money :amount="$item['price'] * $item['quantity']" />
              </span>
            </div>
          </div>
        @endforeach
      </div>
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

    <!-- Addresses -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-20">
      <div class="border border-gray-200 rounded-lg p-16">
        <h3 class="font-medium text-gray-900 mb-12">Rechnungsadresse</h3>
        @if(!empty($invoice_address))
          <div class="text-sm text-gray-600 space-y-4">
            @if(!empty($invoice_address['salutation']))
              <p>{{ $invoice_address['salutation'] }}</p>
            @endif
            <p>{{ $invoice_address['firstname'] ?? '' }} {{ $invoice_address['lastname'] ?? '' }}</p>
            <p>{{ $invoice_address['street'] ?? '' }} {{ $invoice_address['street_number'] ?? '' }}</p>
            <p>{{ $invoice_address['zip'] ?? '' }} {{ $invoice_address['city'] ?? '' }}</p>
            <p>{{ $invoice_address['country'] ?? '' }}</p>
          </div>
        @endif
      </div>

      <div class="border border-gray-200 rounded-lg p-16">
        <h3 class="font-medium text-gray-900 mb-12">Lieferadresse</h3>
        @if(!empty($delivery_address))
          <div class="text-sm text-gray-600 space-y-4">
            @if(!empty($delivery_address['salutation']))
              <p>{{ $delivery_address['salutation'] }}</p>
            @endif
            <p>{{ $delivery_address['firstname'] ?? '' }} {{ $delivery_address['lastname'] ?? '' }}</p>
            <p>{{ $delivery_address['street'] ?? '' }} {{ $delivery_address['street_number'] ?? '' }}</p>
            <p>{{ $delivery_address['zip'] ?? '' }} {{ $delivery_address['city'] ?? '' }}</p>
            <p>{{ $delivery_address['country'] ?? '' }}</p>
          </div>
        @else
          <p class="text-sm text-gray-500">Identisch mit Rechnungsadresse</p>
        @endif
      </div>
    </div>

    <!-- Payment Method -->
    <div class="border border-gray-200 rounded-lg p-16">
      <h3 class="font-medium text-gray-900 mb-12">Zahlungsmethode</h3>
      <div class="flex items-center gap-12 text-gray-600">
        @if($payment_method === 'invoice')
          <i class="ph ph-file-text text-xl"></i>
          <span>Rechnung</span>
        @else
          <i class="ph ph-credit-card text-xl"></i>
          <span>Kreditkarte / Twint</span>
        @endif
      </div>
    </div>

    <!-- Terms and Submit -->
    <div class="space-y-20">
      <label class="flex items-start gap-12 cursor-pointer">
        <input
          type="checkbox"
          wire:model="terms_accepted"
          class="w-20 h-20 mt-2 rounded border-gray-300 text-gray-900 focus:ring-gray-900"
        >
        <span class="text-sm text-gray-600">
          Ich akzeptiere die <a href="/agb" class="underline hover:text-gray-900" target="_blank">Allgemeinen Geschäftsbedingungen</a> und die <a href="/datenschutz" class="underline hover:text-gray-900" target="_blank">Datenschutzerklärung</a>.
        </span>
      </label>

      <button
        type="button"
        wire:click="placeOrder"
        class="w-full py-12 px-24 bg-gray-900 text-white rounded-lg font-medium hover:bg-gray-800 transition-colors"
        wire:loading.attr="disabled"
        wire:loading.class="opacity-50 cursor-not-allowed"
      >
        <span wire:loading.remove>Kostenpflichtig bestellen</span>
        <span wire:loading>Bestellung wird verarbeitet...</span>
      </button>
    </div>

  </div>

</div>
