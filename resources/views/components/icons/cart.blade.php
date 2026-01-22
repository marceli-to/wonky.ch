@props([
  'size' => 32
])
<i class="ph ph-shopping-cart" style="font-size: {{ $size }}px" {{ $attributes->merge(['class' => '']) }}></i>
