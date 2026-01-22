@props(['class' => ''])

<h1 {{ $attributes->merge(['class' => 'text-2xl font-semibold text-gray-900 ' . $class]) }}>
  {{ $slot }}
</h1>
