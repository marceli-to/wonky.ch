<x-layout.app title="Bestellung erfolgreich">

  <div class="max-w-xl mx-auto px-20 lg:px-32 py-48">

    <div class="flex flex-col gap-32">

      <!-- Success Message -->
      <div class="text-center">
        <div class="mb-24">
          <i class="ph ph-check-circle text-6xl text-green-500"></i>
        </div>
        <h1 class="text-2xl font-semibold text-gray-900">Vielen Dank für Ihre Bestellung!</h1>
        <p class="mt-12 text-gray-600">Ihre Zahlung wurde erfolgreich verarbeitet.</p>
      </div>

      <!-- Order Reference -->
      <div class="text-center py-16 border-t border-b border-gray-200">
        <span>Bestellreferenz: <strong>{{ $reference }}</strong></span>
      </div>

      <!-- Order Summary -->
      @if(!empty($cart['items']))
        <div class="space-y-12">
          <h2 class="font-semibold text-gray-900">Bestellübersicht</h2>

          @foreach($cart['items'] as $item)
            <div class="flex justify-between py-12 border-b border-gray-100">
              <span>{{ $item['title'] }} × {{ $item['quantity'] }}</span>
              <x-cart.money :amount="$item['price'] * $item['quantity']" />
            </div>
          @endforeach

          <div class="flex justify-between py-12 font-semibold">
            <span>Total</span>
            <x-cart.money :amount="$cart['total']" />
          </div>
        </div>
      @endif

      <!-- Continue Shopping -->
      <div class="text-center">
        <a
          href="{{ route('page.landing') }}"
          class="inline-block py-12 px-32 bg-gray-900 text-white rounded-lg font-medium hover:bg-gray-800 transition-colors">
          Weiter einkaufen
        </a>
      </div>

    </div>

  </div>

</x-layout.app>
