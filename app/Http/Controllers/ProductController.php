<?php

namespace App\Http\Controllers;

use App\Actions\Product\Find;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display all products.
     */
    public function index(): View
    {
        $products = Product::where('published', true)
            ->whereNull('parent_id')
            ->with(['previewImage', 'children', 'categories'])
            ->orderBy('title')
            ->get();

        return view('pages.product.index', compact('products'));
    }

    /**
     * Display the specified product within a category.
     */
    public function show(Category $category, Product $product): View
    {
        // Only show published products
        if (! $product->published) {
            abort(404);
        }

        // Load product with all images
        $product = (new Find)->execute($product);

        return view('pages.product.show', compact('category', 'product'));
    }

    /**
     * Display a product directly (without category context).
     */
    public function showDirect(Product $product): View
    {
        // Only show published products
        if (! $product->published) {
            abort(404);
        }

        // Load product with all images
        $product = (new Find)->execute($product);

        // Get first category or null
        $category = $product->categories->first();

        return view('pages.product.show', compact('category', 'product'));
    }
}
