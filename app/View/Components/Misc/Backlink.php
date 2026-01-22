<?php

namespace App\View\Components\Misc;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * BackUrl component that generates a smart back URL.
 *
 * Returns javascript:history.back() if the referrer is from the same domain,
 * otherwise returns a fallback URL.
 *
 * Usage examples:
 *
 * Basic usage with default fallback:
 * <a href="<x-misc.back-url />">Back</a>
 *
 * With custom fallback:
 * <a href="<x-misc.back-url fallback="/categories" />">Back</a>
 *
 * With route fallback:
 * <a :href="<x-misc.back-url :fallback="route('page.landing')" />">Back</a>
 */
class Backlink extends Component
{
    /**
     * The fallback URL to use if there's no same-domain referrer.
     */
    public string $fallback;

    /**
     * The calculated back URL.
     */
    public string $url;

    /**
     * Create a new component instance.
     */
    public function __construct(string $fallback = '/')
    {
        $this->fallback = $fallback;
        $this->url = $this->calculateUrl();
    }

    /**
     * Calculate the back URL based on referrer.
     */
    protected function calculateUrl(): string
    {
        $referrer = request()->headers->get('referer');
        $currentHost = request()->getHost();

        // Check if referrer exists and is from the same domain
        if ($referrer && parse_url($referrer, PHP_URL_HOST) === $currentHost) {
            // Referrer is from same site, use javascript:history.back()
            return 'javascript:history.back()';
        }

        // No referrer or external referrer, use fallback URL
        return $this->fallback;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.misc.backlink');
    }
}
