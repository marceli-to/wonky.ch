<picture>
	@foreach($sources as $source)
		<source
			srcset="{{ $source['srcset'] }}"
			type="{{ $source['type'] }}"
			@if($source['media']) media="{{ $source['media'] }}" @endif
		>
	@endforeach

	<img
		src="{{ $fallbackUrl }}"
		alt="{{ $alt }}"
		@if($width) width="{{ $width }}" @endif
		@if($height) height="{{ $height }}" @endif
		@if($class) class="{{ $class }}" @endif
		loading="{{ $loading }}"
		{{ $attributes }}
	>
</picture>