@props([
  'type' => 'error',
])
@php
  $bgColor = match($type) {
    'error' => 'bg-maroon',
    'success' => 'bg-olive',
    default => 'bg-maroon',
  };
@endphp
<div {{ $attributes->merge(['class' => $bgColor . ' absolute w-full h-80 -top-(--header-height-sm) lg:-top-(--header-height-lg) z-50 flex items-center text-white px-20 py-20']) }}>
  {{ $slot }}
</div>
