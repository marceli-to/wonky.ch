@props([
  'title' => 'Shop',
])

<x-layout.head :title="$title" />
<x-layout.body>
  <x-layout.header />
  <x-layout.main>
    {{ $slot ?? '' }}
  </x-layout.main>
</x-layout.body>
<x-layout.footer />
