@props([
  'size' => 'lg'
])
@php
  $sizeMap = [
    'lg' => 30,
    'md' => 22,
    'sm' => 12,
  ];
  $pixelSize = $sizeMap[$size] ?? 30;
@endphp
<i class="ph ph-x" style="font-size: {{ $pixelSize }}px" {{ $attributes->merge(['class' => '']) }}></i>
