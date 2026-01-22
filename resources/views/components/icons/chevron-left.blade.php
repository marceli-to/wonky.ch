@props([
  'size' => 22
])
<i class="ph ph-caret-left" style="font-size: {{ $size }}px" {{ $attributes->merge(['class' => '']) }}></i>
