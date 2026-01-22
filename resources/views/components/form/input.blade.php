@props([
  'type' => 'text',
  'id' => '',
  'placeholder' => '',
  'error' => false,
])
<input 
  type="{{ $type }}" 
  id="{{ $id }}"
  placeholder="{{ $placeholder }}"
  {{ $attributes->merge(['class' => 'w-full bg-transparent outline-none placeholder:text-ash' . ($error ? '  placeholder:text-maroon lg:placeholder:text-ash' : '')]) }} />
