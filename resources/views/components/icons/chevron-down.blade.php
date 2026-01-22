@props([
  'size' => 14
])
<i class="ph ph-caret-down" style="font-size: {{ $size }}px" {{ $attributes->merge(['class' => '']) }}></i>
