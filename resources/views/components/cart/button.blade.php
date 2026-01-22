@props(['inCart' => false, 'disabled' => false])

<button
  type="button"
  wire:click="addToCart"
  wire:loading.attr="disabled"
  @class([
    'w-full py-12 px-24 rounded-lg font-medium transition-colors',
    'bg-gray-900 text-white hover:bg-gray-800' => !$inCart && !$disabled,
    'bg-gray-300 text-gray-500 cursor-not-allowed' => $inCart || $disabled,
  ])
  {{ ($inCart || $disabled) ? 'disabled' : '' }}>
  <span wire:loading.remove wire:target="addToCart">
    @if($inCart)
      Im Warenkorb
    @else
      In den Warenkorb
    @endif
  </span>
  <span wire:loading wire:target="addToCart">Wird hinzugefügt...</span>
</button>
