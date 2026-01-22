<x-layout.app title="Zahlung">

  <div class="max-w-4xl mx-auto px-20 lg:px-32">

    <x-menu.checkout.menu :currentStep="3" />

    <livewire:checkout.payment />

  </div>

</x-layout.app>
