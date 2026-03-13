<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|integer|min:1'
        ]);

        $product = Product::create([
            'name' => $request->name,
            'amount' => $request->amount
        ]);

        return response()->json($product, 201);
    }
}