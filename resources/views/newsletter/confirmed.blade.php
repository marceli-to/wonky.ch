<x-layout.app title="Anmeldung bestätigt">
  <div class="max-w-7xl mx-auto px-20 lg:px-32 py-64">
    <div class="max-w-md mx-auto text-center">
      <div class="w-64 h-64 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-24">
        <i class="ph ph-check text-3xl text-green-600"></i>
      </div>
      <h1 class="text-2xl font-bold mb-16">Anmeldung bestätigt!</h1>
      <p class="text-gray-600 mb-32">
        Vielen Dank! Ihre Newsletter-Anmeldung wurde erfolgreich bestätigt. Sie erhalten ab sofort unseren Newsletter.
      </p>
      <a href="{{ url('/') }}" class="inline-block bg-gray-900 text-white px-24 py-12 rounded-lg hover:bg-gray-800 transition">
        Zurück zur Startseite
      </a>
    </div>
  </div>
</x-layout.app>
