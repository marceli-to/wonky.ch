@props([
  'size' => 40
])
<i class="ph ph-file-text" style="font-size: {{ $size }}px" {{ $attributes->merge(['class' => '']) }}></i>
