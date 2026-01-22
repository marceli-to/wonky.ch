@props([
  'amount' => '0.00',
])
<span {{ $attributes }}>
  CHF {{ number_format($amount, 2, '.', "'") }}
</span>
