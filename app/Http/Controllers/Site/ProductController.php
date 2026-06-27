<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        return view('site.products', [
            'products' => Product::query()->published()->ordered()->get(),
        ]);
    }
}
