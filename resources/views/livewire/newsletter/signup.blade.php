<div>
  @if($success)
    <div class="bg-green-50 border border-green-200 rounded-lg p-16 text-green-800">
      <p class="font-medium">Fast geschafft!</p>
      <p class="text-sm mt-4">Wir haben Ihnen eine E-Mail gesendet. Bitte klicken Sie auf den Bestätigungslink, um Ihre Anmeldung abzuschliessen.</p>
    </div>
  @else
    <form wire:submit="subscribe" class="flex flex-col sm:flex-row gap-12">
      <div class="flex-1">
        <input
          type="email"
          id="newsletter-email"
          wire:model="email"
          placeholder="E-Mail Adresse"
          class="w-full px-16 py-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent transition"
          required
        >
        @error('email')
          <span class="text-red-500 text-sm mt-4 block">{{ $message }}</span>
        @enderror
      </div>

      <button
        type="submit"
        class="bg-gray-900 text-white px-24 py-12 rounded-lg hover:bg-gray-800 transition font-medium whitespace-nowrap"
        wire:loading.attr="disabled"
        wire:loading.class="opacity-50 cursor-not-allowed"
      >
        <span wire:loading.remove>Anmelden</span>
        <span wire:loading>...</span>
      </button>
    </form>

    @if($error)
      <p class="text-red-500 text-sm mt-8">{{ $error }}</p>
    @endif
  @endif
</div>
