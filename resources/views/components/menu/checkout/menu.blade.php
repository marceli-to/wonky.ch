@props(['currentStep' => 1])

@php
  $steps = [
    1 => ['label' => 'Warenkorb', 'icon' => 'ph-shopping-cart', 'route' => 'page.checkout.basket'],
    2 => ['label' => 'Adresse', 'icon' => 'ph-map-pin', 'route' => 'page.checkout.invoice-address'],
    3 => ['label' => 'Zahlung', 'icon' => 'ph-credit-card', 'route' => 'page.checkout.payment'],
  ];
@endphp

<nav class="py-32">
  <div class="max-w-md mx-auto">
    <div class="flex items-center justify-between relative">

      {{-- Connecting line (background) --}}
      <div class="absolute top-24 left-0 right-0 h-2 bg-gray-200 mx-48"></div>

      {{-- Progress line --}}
      <div
        class="absolute top-24 left-0 h-2 bg-green-500 mx-48 transition-all duration-300"
        style="width: calc({{ ($currentStep - 1) / (count($steps) - 1) * 100 }}% - 96px);">
      </div>

      @foreach($steps as $step => $data)
        <div class="relative z-10 flex flex-col items-center">
          {{-- Circle --}}
          @php
            $isCompleted = $step < $currentStep;
            $isCurrent = $step === $currentStep;
            $isPending = $step > $currentStep;
          @endphp

          <div @class([
            'w-48 h-48 rounded-full flex items-center justify-center transition-colors',
            'bg-green-500 text-white' => $isCompleted || $isCurrent,
            'bg-gray-200 text-gray-400' => $isPending,
          ])>
            <i class="ph {{ $data['icon'] }} text-xl"></i>
          </div>

          {{-- Label --}}
          <span @class([
            'mt-8 text-sm',
            'font-semibold text-gray-900' => $isCurrent,
            'text-gray-500' => !$isCurrent,
          ])>
            {{ $data['label'] }}
          </span>
        </div>
      @endforeach

    </div>
  </div>
</nav>
