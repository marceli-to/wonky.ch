<div>

  @if($order)
    <div class="space-y-32">

      <!-- Thank you message -->
      <div class="text-center">
        <div class="mb-16">
          <i class="ph ph-check-circle text-6xl text-green-500"></i>
        </div>
        <h1 class="text-2xl font-semibold text-gray-900 mb-8">Vielen Dank für Ihre Bestellung!</h1>
        <p class="text-gray-600">Die Bestellbestätigung erhalten Sie per E-Mail an {{ $order->invoice_email }}.</p>
      </div>

      <!-- Order Items -->
      <div class="border border-gray-200 rounded-lg divide-y divide-gray-200">
        @foreach($order->items as $item)
          <div class="p-16 flex justify-between">
            <div>
              <span class="font-medium text-gray-900">{{ $item->product_name }}</span>
              <span class="text-gray-500">× {{ $item->quantity }}</span>
            </div>
            <x-cart.money :amount="$item->product_price * $item->quantity" />
          </div>
        @endforeach
      </div>

      <!-- Totals -->
      <div class="border border-gray-200 rounded-lg p-16 space-y-12">
        @if($order->shipping > 0)
          <div class="flex justify-between text-gray-600">
            <span>Versand</span>
            <x-cart.money :amount="$order->shipping" />
          </div>
        @endif
        <div class="flex justify-between text-gray-600">
          <span>Zwischensumme</span>
          <x-cart.money :amount="$order->subtotal" />
        </div>
        <div class="flex justify-between text-gray-600">
          <span>MwSt. ({{ config('invoice.tax_rate', 8.1) }}%)</span>
          <x-cart.money :amount="$order->tax" />
        </div>
        <div class="flex justify-between text-lg font-semibold text-gray-900 pt-12 border-t border-gray-200">
          <span>Total</span>
          <x-cart.money :amount="$order->total" />
        </div>
      </div>

      <!-- Payment info -->
      <div class="border border-gray-200 rounded-lg p-16">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-12 text-gray-600">
            @if($order->payment_method === 'invoice')
              <i class="ph ph-file-text text-xl"></i>
              <span>Rechnung</span>
            @else
              <i class="ph ph-credit-card text-xl"></i>
              <span>Kreditkarte / Twint</span>
            @endif
          </div>
          <span class="text-sm text-gray-500">{{ $order->created_at->format('d.m.Y, H:i') }}</span>
        </div>
      </div>

      <!-- Addresses -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-20">
        <div class="border border-gray-200 rounded-lg p-16">
          <h3 class="font-medium text-gray-900 mb-12">Rechnungsadresse</h3>
          <div class="text-sm text-gray-600 space-y-4">
            @if($order->invoice_salutation)
              <p>{{ $order->invoice_salutation }}</p>
            @endif
            <p>{{ $order->invoice_name }}</p>
            <p>{{ $order->invoice_address }}</p>
            <p>{{ $order->invoice_location }}</p>
            <p>{{ $order->invoice_country }}</p>
          </div>
        </div>

        <div class="border border-gray-200 rounded-lg p-16">
          <h3 class="font-medium text-gray-900 mb-12">Lieferadresse</h3>
          @if($order->use_invoice_address)
            <p class="text-sm text-gray-500">Identisch mit Rechnungsadresse</p>
          @else
            <div class="text-sm text-gray-600 space-y-4">
              @if($order->shipping_salutation)
                <p>{{ $order->shipping_salutation }}</p>
              @endif
              <p>{{ $order->shipping_name }}</p>
              <p>{{ $order->shipping_address }}</p>
              <p>{{ $order->shipping_location }}</p>
              <p>{{ $order->shipping_country }}</p>
            </div>
          @endif
        </div>
      </div>

      <!-- Back to Shop -->
      <div class="text-center">
        <a
          href="{{ route('page.landing') }}"
          class="inline-block py-12 px-32 bg-gray-900 text-white rounded-lg font-medium hover:bg-gray-800 transition-colors"
        >
          Weiter einkaufen
        </a>
      </div>

    </div>
  @else
    <div class="text-center py-48">
      <i class="ph ph-warning text-4xl text-gray-400 mb-16"></i>
      <p class="text-gray-500">Bestellung nicht gefunden.</p>
    </div>
  @endif

</div>
