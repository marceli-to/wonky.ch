<div>

  @if($errors->any())
    <div class="mb-24 p-16 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">
      Bitte füllen Sie die markierten Pflichtfelder aus.
    </div>
  @endif

  <form wire:submit="save" class="space-y-20">

    <div>
      <label for="salutation" class="block text-sm font-medium text-gray-700 mb-8">Anrede</label>
      <input
        type="text"
        id="salutation"
        wire:model="salutation"
        placeholder="Herr / Frau"
        class="w-full px-16 py-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent transition"
      >
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-20">
      <div>
        <label for="firstname" class="block text-sm font-medium text-gray-700 mb-8">
          Vorname <span class="text-red-500">*</span>
        </label>
        <input
          type="text"
          id="firstname"
          wire:model="firstname"
          placeholder="Vorname"
          class="w-full px-16 py-12 border rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent transition {{ $errors->has('firstname') ? 'border-red-500' : 'border-gray-300' }}"
          required
        >
        @error('firstname')
          <span class="text-red-500 text-sm mt-4 block">{{ $message }}</span>
        @enderror
      </div>

      <div>
        <label for="lastname" class="block text-sm font-medium text-gray-700 mb-8">
          Nachname <span class="text-red-500">*</span>
        </label>
        <input
          type="text"
          id="lastname"
          wire:model="lastname"
          placeholder="Nachname"
          class="w-full px-16 py-12 border rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent transition {{ $errors->has('lastname') ? 'border-red-500' : 'border-gray-300' }}"
          required
        >
        @error('lastname')
          <span class="text-red-500 text-sm mt-4 block">{{ $message }}</span>
        @enderror
      </div>
    </div>

    <div class="grid grid-cols-3 gap-20">
      <div class="col-span-2">
        <label for="street" class="block text-sm font-medium text-gray-700 mb-8">
          Strasse <span class="text-red-500">*</span>
        </label>
        <input
          type="text"
          id="street"
          wire:model="street"
          placeholder="Strasse"
          class="w-full px-16 py-12 border rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent transition {{ $errors->has('street') ? 'border-red-500' : 'border-gray-300' }}"
          required
        >
        @error('street')
          <span class="text-red-500 text-sm mt-4 block">{{ $message }}</span>
        @enderror
      </div>

      <div>
        <label for="street_number" class="block text-sm font-medium text-gray-700 mb-8">Nr.</label>
        <input
          type="text"
          id="street_number"
          wire:model="street_number"
          placeholder="Nr."
          class="w-full px-16 py-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent transition"
        >
      </div>
    </div>

    <div class="grid grid-cols-3 gap-20">
      <div>
        <label for="zip" class="block text-sm font-medium text-gray-700 mb-8">
          PLZ <span class="text-red-500">*</span>
        </label>
        <input
          type="text"
          id="zip"
          wire:model="zip"
          placeholder="PLZ"
          class="w-full px-16 py-12 border rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent transition {{ $errors->has('zip') ? 'border-red-500' : 'border-gray-300' }}"
          required
        >
        @error('zip')
          <span class="text-red-500 text-sm mt-4 block">{{ $message }}</span>
        @enderror
      </div>

      <div class="col-span-2">
        <label for="city" class="block text-sm font-medium text-gray-700 mb-8">
          Ort <span class="text-red-500">*</span>
        </label>
        <input
          type="text"
          id="city"
          wire:model="city"
          placeholder="Ort"
          class="w-full px-16 py-12 border rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent transition {{ $errors->has('city') ? 'border-red-500' : 'border-gray-300' }}"
          required
        >
        @error('city')
          <span class="text-red-500 text-sm mt-4 block">{{ $message }}</span>
        @enderror
      </div>
    </div>

    <div>
      <label for="country" class="block text-sm font-medium text-gray-700 mb-8">
        Land <span class="text-red-500">*</span>
      </label>
      <input
        type="text"
        id="country"
        wire:model="country"
        placeholder="Schweiz"
        class="w-full px-16 py-12 border rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent transition {{ $errors->has('country') ? 'border-red-500' : 'border-gray-300' }}"
        required
      >
      @error('country')
        <span class="text-red-500 text-sm mt-4 block">{{ $message }}</span>
      @enderror
    </div>

    <div>
      <label for="email" class="block text-sm font-medium text-gray-700 mb-8">
        E-Mail <span class="text-red-500">*</span>
      </label>
      <input
        type="email"
        id="email"
        wire:model="email"
        placeholder="ihre@email.ch"
        class="w-full px-16 py-12 border rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent transition {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }}"
        required
      >
      @error('email')
        <span class="text-red-500 text-sm mt-4 block">{{ $message }}</span>
      @enderror
    </div>

    <div>
      <label for="phone" class="block text-sm font-medium text-gray-700 mb-8">
        Telefon <span class="text-red-500">*</span>
      </label>
      <input
        type="tel"
        id="phone"
        wire:model="phone"
        placeholder="+41 79 123 45 67"
        class="w-full px-16 py-12 border rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent transition {{ $errors->has('phone') ? 'border-red-500' : 'border-gray-300' }}"
        required
      >
      @error('phone')
        <span class="text-red-500 text-sm mt-4 block">{{ $message }}</span>
      @enderror
    </div>

    <div class="pt-16">
      <button
        type="submit"
        class="w-full py-12 px-24 bg-gray-900 text-white rounded-lg font-medium hover:bg-gray-800 transition-colors"
        wire:loading.attr="disabled"
        wire:loading.class="opacity-50 cursor-not-allowed"
      >
        <span wire:loading.remove>Weiter zur Lieferadresse</span>
        <span wire:loading>Wird gespeichert...</span>
      </button>
    </div>

  </form>

</div>
