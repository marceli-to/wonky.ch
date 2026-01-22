<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Glide\Server;
use League\Glide\ServerFactory;

class ImageController extends Controller
{
    protected Server $server;

    public function __construct()
    {
        // Using Imagick driver for AVIF and WebP support
        // GD driver lacks AVIF encoding support
        $this->server = ServerFactory::create([
            'source' => storage_path('app/public'),
            'cache' => storage_path('app/.glide-cache'),
            'driver' => 'imagick',
        ]);
    }

    /**
     * Generate and serve manipulated images using Glide.
     */
    public function show(Request $request, string $path): Response
    {
        // makeImage returns the cached file path
        $cachedPath = $this->server->makeImage($path, $request->all());

        // Get the cache filesystem
        $cache = $this->server->getCache();

        // Read the image content from cache
        $imageContent = $cache->read($cachedPath);
        $mimeType = $cache->mimeType($cachedPath);

        return response($imageContent, 200)
            ->header('Content-Type', $mimeType)
            ->header('Cache-Control', 'max-age=31536000, public')
            ->header('Expires', now()->addYear()->toRfc7231String());
    }
}
