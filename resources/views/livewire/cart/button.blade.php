<div class="flex flex-col gap-16">

  <x-cart.quantity :quantity="$quantity" :maxStock="$maxStock" />

  @if($showButton)
    <x-cart.button :inCart="$inCart" />
  @endif

</div>
