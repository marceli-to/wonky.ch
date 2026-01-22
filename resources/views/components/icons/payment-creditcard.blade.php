@props([
  'size' => 40
])
<i class="ph ph-credit-card" style="font-size: {{ $size }}px" {{ $attributes->merge(['class' => '']) }}></i>
