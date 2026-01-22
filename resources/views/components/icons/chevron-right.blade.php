@props([
  'size' => 'lg'
])
@php
  $sizeMap = [
    'lg' => 22,
    'sm' => 12,
  ];
  $pixelSize = $sizeMap[$size] ?? 22;
@endphp
<i class="ph ph-caret-right" style="font-size: {{ $pixelSize }}px" {{ $attributes->merge(['class' => '']) }}></i>
