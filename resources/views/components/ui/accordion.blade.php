@props(['title', 'open' => false])

<div x-data="{ open: {{ $open ? 'true' : 'false' }} }">
  <button
    type="button"
    @click="open = !open"
    class="min-h-40 w-full flex items-center justify-between border-t border-b border-black cursor-pointer"
  >
    <span class="font-sans">{{ $title }}</span>
    <x-icons.chevron-down
      class="transition-transform duration-200"
      ::class="{ 'rotate-180': open }"
    />
  </button>

  <div x-show="open" x-collapse>
    {{ $slot }}
  </div>
</div>
