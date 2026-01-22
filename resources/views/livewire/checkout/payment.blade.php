<div>

  <form wire:submit="save" class="space-y-24">

    <h2 class="text-lg font-medium text-gray-900">Zahlungsmethode wählen</h2>

    <div class="space-y-12">

      <!-- Credit Card / Twint -->
      <label class="flex items-center gap-16 p-20 border rounded-lg cursor-pointer transition {{ $payment_method === 'card' ? 'border-gray-900 bg-gray-50' : 'border-gray-200 hover:border-gray-300' }}">
        <input
          type="radio"
          name="payment_method"
          value="card"
          wire:model.live="payment_method"
          class="w-20 h-20 text-gray-900 border-gray-300 focus:ring-gray-900"
        >
        <div class="flex items-center gap-16">
          <i class="ph ph-credit-card text-2xl text-gray-600"></i>
          <div>
            <span class="font-medium text-gray-900">Karte / Twint</span>
            <p class="text-sm text-gray-500">Kreditkarte, Apple Pay, Google Pay, Twint</p>
          </div>
        </div>
      </label>

      <!-- Invoice -->
      <label class="flex items-center gap-16 p-20 border rounded-lg cursor-pointer transition {{ $payment_method === 'invoice' ? 'border-gray-900 bg-gray-50' : 'border-gray-200 hover:border-gray-300' }}">
        <input
          type="radio"
          name="payment_method"
          value="invoice"
          wire:model.live="payment_method"
          class="w-20 h-20 text-gray-900 border-gray-300 focus:ring-gray-900"
        >
        <div class="flex items-center gap-16">
          <i class="ph ph-file-text text-2xl text-gray-600"></i>
          <div>
            <span class="font-medium text-gray-900">Rechnung</span>
            <p class="text-sm text-gray-500">Zahlung innert 30 Tagen</p>
          </div>
        </div>
      </label>

    </div>

    <div class="pt-16">
      <button
        type="submit"
        class="w-full py-12 px-24 bg-gray-900 text-white rounded-lg font-medium hover:bg-gray-800 transition-colors"
        wire:loading.attr="disabled"
        wire:loading.class="opacity-50 cursor-not-allowed"
      >
        <span wire:loading.remove>Weiter zur Zusammenfassung</span>
        <span wire:loading>Wird gespeichert...</span>
      </button>
    </div>

  </form>

</div>
