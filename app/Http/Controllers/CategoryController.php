<?php

namespace App\Http\Controllers;

use App\Actions\Category\GetProducts as GetProductsAction;
use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a category with its products.
     */
    public function get(Category $category): View
    {
        $products = (new GetProductsAction)->execute($category);

        return view('pages.category.index', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}
