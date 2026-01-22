<?php

namespace App\Actions\Product;

use App\Models\Product;

class Find
{
    /**
     * Find a product with all images.
     */
    public function execute(Product $product): Product
    {
        return $product->load(['images', 'children']);
    }
}
