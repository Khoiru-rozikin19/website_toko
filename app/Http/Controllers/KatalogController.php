<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KatalogController extends Controller
{
    /**
     * Display the product catalog page with real database products.
     */
    public function index(Request $request): View
    {
        $products = Product::active()->orderBy('id', 'asc')->get();

        return view('katalog.index', compact('products'));
    }
}
