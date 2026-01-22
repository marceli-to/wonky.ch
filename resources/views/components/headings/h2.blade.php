@props(['class' => ''])

<h2 {{ $attributes->merge(['class' => 'text-xl font-semibold text-gray-900 ' . $class]) }}>
  {{ $slot }}
</h2>
