@props([
  'size' => 12
])
<i class="ph ph-plus" style="font-size: {{ $size }}px" {{ $attributes->merge(['class' => '']) }}></i>
