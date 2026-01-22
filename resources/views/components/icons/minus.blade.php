@props([
  'size' => 12
])
<i class="ph ph-minus" style="font-size: {{ $size }}px" {{ $attributes->merge(['class' => '']) }}></i>
