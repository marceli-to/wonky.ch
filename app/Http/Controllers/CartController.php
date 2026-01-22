<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the shopping cart page.
     */
    public function index(): View
    {
        return view('pages.cart');
    }
}
