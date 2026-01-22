<x-layout.app title="Warenkorb">

  <div class="max-w-4xl mx-auto px-20 lg:px-32">

    <x-menu.checkout.menu :currentStep="1" />

    <livewire:cart.cart />

  </div>

</x-layout.app>
