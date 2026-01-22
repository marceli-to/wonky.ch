@props([
  'quantity',
  'maxStock' => null,
  'class' => ''
])

<div class="flex items-center border border-gray-300 rounded-lg h-48 {{ $class }}">

  <button
    type="button"
    wire:click="decrement"
    class="w-48 h-full flex items-center justify-center text-gray-600 hover:text-gray-900 disabled:opacity-50"
    {{ $quantity <= 1 ? 'disabled' : '' }}>
    <i class="ph ph-minus"></i>
  </button>

  <input
    type="number"
    wire:model.blur="quantity"
    min="1"
    {{ $maxStock ? 'max=' . $maxStock : '' }}
    class="w-64 h-full text-center font-medium bg-transparent border-x border-gray-300 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
  />

  <button
    type="button"
    wire:click="increment"
    class="w-48 h-full flex items-center justify-center text-gray-600 hover:text-gray-900 disabled:opacity-50"
    {{ $maxStock && $quantity >= $maxStock ? 'disabled' : '' }}>
    <i class="ph ph-plus"></i>
  </button>

</div>
