<?php

namespace App\View\Components\Media;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Image component with automatic format conversion (AVIF, WebP, JPEG).
 *
 * Usage examples:
 *
 * Basic usage:
 * <x-media.image src="products/image.jpg" alt="Product" :width="800" />
 *
 * With custom formats:
 * <x-media.image
 *     src="products/image.jpg"
 *     alt="Product"
 *     :width="400"
 *     :height="300"
 *     fit="crop"
 *     :quality="90"
 *     :formats="['avif', 'webp', 'jpg']"
 * />
 *
 * With responsive breakpoints:
 * <x-media.image
 *     src="products/image.jpg"
 *     alt="Product"
 *     :breakpoints="[
 *         ['media' => '(min-width: 1280px)', 'width' => 1580, 'height' => 1200],
 *         ['media' => '(min-width: 1024px)', 'width' => 1280, 'height' => 960],
 *         ['media' => '(min-width: 768px)', 'width' => 1024, 'height' => 768],
 *         ['width' => 768, 'height' => 576],
 *     ]"
 *     :formats="['avif', 'webp', 'jpg']"
 * />
 *
 * With custom class and eager loading:
 * <x-media.image
 *     src="products/image.jpg"
 *     alt="Hero Image"
 *     :width="1200"
 *     class="rounded-lg shadow-xl"
 *     loading="eager"
 * />
 */
class Image extends Component
{
    /**
     * The image source path.
     */
    public string $src;

    /**
     * The image alt text.
     */
    public string $alt;

    /**
     * The image width.
     */
    public ?int $width;

    /**
     * The image height.
     */
    public ?int $height;

    /**
     * The fit mode (crop, contain, fill, etc.).
     */
    public string $fit;

    /**
     * The image quality (0-100).
     */
    public int $quality;

    /**
     * The formats to generate (avif, webp, jpg).
     */
    public array $formats;

    /**
     * Additional CSS classes.
     */
    public string $class;

    /**
     * Loading strategy (lazy, eager).
     */
    public string $loading;

    /**
     * Responsive breakpoints configuration.
     */
    public array $breakpoints;

    /**
     * Prepared source URLs for each format and breakpoint.
     */
    public array $sources = [];

    /**
     * Fallback image URL.
     */
    public string $fallbackUrl;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $src,
        string $alt = '',
        ?int $width = null,
        ?int $height = null,
        string $fit = 'crop',
        int $quality = 85,
        array $formats = ['avif', 'webp', 'jpg'],
        array $breakpoints = [],
        string $class = '',
        string $loading = 'lazy'
    ) {
        $this->src = $src;
        $this->alt = $alt;
        $this->width = $width;
        $this->height = $height;
        $this->fit = $fit;
        $this->quality = $quality;
        $this->formats = $formats;
        $this->breakpoints = $breakpoints;
        $this->class = $class;
        $this->loading = $loading;

        // Use responsive breakpoints if provided
        if (! empty($this->breakpoints)) {
            $this->buildResponsiveSources();
        } else {
            $this->buildSimpleSources();
        }
    }

    /**
     * Build sources for responsive breakpoints.
     */
    protected function buildResponsiveSources(): void
    {
        foreach ($this->breakpoints as $breakpoint) {
            $bpWidth = $breakpoint['width'] ?? $this->width;
            $bpHeight = $breakpoint['height'] ?? $this->height;
            $media = $breakpoint['media'] ?? null;

            foreach ($this->formats as $format) {
                $this->sources[] = [
                    'srcset' => $this->buildUrl($format, $bpWidth, $bpHeight),
                    'type' => $this->getMimeType($format),
                    'media' => $media,
                ];
            }
        }

        // Set fallback to the last breakpoint's jpg version
        $lastBreakpoint = end($this->breakpoints);
        $this->fallbackUrl = $this->buildUrl('jpg', $lastBreakpoint['width'] ?? $this->width, $lastBreakpoint['height'] ?? $this->height);
    }

    /**
     * Build sources for simple (non-responsive) images.
     */
    protected function buildSimpleSources(): void
    {
        // Prepare source URLs for each format
        foreach ($this->formats as $format) {
            if ($format !== 'jpg' && $format !== 'jpeg') {
                $this->sources[] = [
                    'srcset' => $this->buildUrl($format),
                    'type' => $this->getMimeType($format),
                    'media' => null,
                ];
            }
        }

        // Prepare fallback URL
        $this->fallbackUrl = $this->buildUrl('jpg');
    }

    /**
     * Build the image URL with parameters.
     */
    public function buildUrl(?string $format = null, ?int $width = null, ?int $height = null): string
    {
        $params = [];

        $useWidth = $width ?? $this->width;
        $useHeight = $height ?? $this->height;

        if ($useWidth) {
            $params[] = 'w='.$useWidth;
        }

        if ($useHeight) {
            $params[] = 'h='.$useHeight;
        }

        if ($this->fit) {
            $params[] = 'fit='.$this->fit;
        }

        if ($format) {
            $params[] = 'fm='.$format;
        }

        $params[] = 'q='.$this->quality;

        $queryString = implode('&', $params);

        return '/img/'.$this->src.($queryString ? '?'.$queryString : '');
    }

    /**
     * Get MIME type for format.
     */
    public function getMimeType(string $format): string
    {
        return match ($format) {
            'avif' => 'image/avif',
            'webp' => 'image/webp',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            default => 'image/jpeg',
        };
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.media.image');
    }
}
