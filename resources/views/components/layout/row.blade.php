@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'flex py-16 border-t border-gray-200 ' . $class]) }}>
  {{ $slot }}
</div>
