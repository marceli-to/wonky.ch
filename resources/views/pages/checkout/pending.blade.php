<x-layout.app title="Zahlung wird verarbeitet">

  <div class="max-w-xl mx-auto px-20 lg:px-32 py-48">

    <div class="flex flex-col gap-32">

      <!-- Pending Message -->
      <div class="text-center">
        <div class="mb-24">
          <i class="ph ph-clock text-6xl text-yellow-500 animate-pulse"></i>
        </div>
        <h1 class="text-2xl font-semibold text-gray-900">Zahlung wird verarbeitet</h1>
        <p class="mt-12 text-gray-600">Ihre Zahlung wird noch verarbeitet. Sie erhalten eine Bestätigung per E-Mail.</p>
      </div>

      <!-- Order Reference -->
      <div class="text-center py-16 border-t border-b border-gray-200">
        <span>Bestellreferenz: <strong>{{ $reference }}</strong></span>
      </div>

      <!-- Continue Shopping -->
      <div class="text-center">
        <a
          href="{{ route('page.landing') }}"
          class="inline-block py-12 px-32 bg-gray-900 text-white rounded-lg font-medium hover:bg-gray-800 transition-colors">
          Zurück zur Startseite
        </a>
      </div>

    </div>

  </div>

</x-layout.app>
