@props([
  'for' => '',
  'required' => false,
  'error' => false,
])
<label 
  for="{{ $for }}" 
  {{ $attributes->merge(['class' => 'flex items-center h-40' . ($error ? ' text-maroon' : '')]) }}>
  {{ $slot }}@if($required) *@endif
</label>
