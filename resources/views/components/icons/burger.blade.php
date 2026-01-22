@props([
  'size' => 32
])
<i class="ph ph-list" style="font-size: {{ $size }}px" {{ $attributes->merge(['class' => '']) }}></i>
