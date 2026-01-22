@props([
  'class' => '',
  'route' => '',
  'title' => '',
  'type' => 'link',
])
  @if($type === 'submit' || $type === 'button')
    <button 
      {{ $type === 'submit' ? 'type=submit' : 'type=button' }}
      {{ $attributes }}
      class="bg-white border border-black flex items-center justify-between leading-none font-sans h-40 px-20 w-full cursor-pointer {{ $class }}">
      {{ $title }}
      <x-icons.chevron-right size="sm" class="w-8 h-12 shrink-0" />
    </button>
  @else
    <a 
      href="{{ $route }}"
      aria-label="{{ $title }}"
      class="bg-white border border-black flex items-center justify-between leading-none font-sans h-40 px-20 cursor-pointer {{ $class }}">
      {{ $title }}
      <x-icons.chevron-right size="sm" class="w-8 h-12 shrink-0" />
    </a>
  @endif
