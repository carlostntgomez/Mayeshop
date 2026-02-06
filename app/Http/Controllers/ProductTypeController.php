<?php

namespace App\Http\Controllers;

use App\Models\ProductType;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Color;
use App\Models\Tag;

class ProductTypeController extends Controller
{
    public function show(Request $request, ProductType $productType)
    {
        $products = $productType->products();

        if ($request->has('search')) {
            $products->where('products.name', 'like', '%' . $request->search . '%')
                     ->orWhere('products.short_description', 'like', '%' . $request->search . '%');
        }



        if ($request->has('tag')) {
            $products->whereHas('tags', function ($query) use ($request) {
                $query->where('slug', $request->tag);
            });
        }

        if ($request->has('category')) {
            $products->whereHas('category', function ($query) use ($request) {
                $query->where('slug', $request->category);
            });
        }

        if ($request->has('color')) {
            $products->whereHas('colors', function ($query) use ($request) {
                $query->where('name', $request->color);
            });
        }



        $products = $products->paginate(24); // Mostrar 24 productos por página

        $categories = $productType->categories()->whereHas('products')->get();
        $colors = Color::whereHas('products', function ($query) use ($productType) {
            $query->where('product_type_id', $productType->id);
        })->get();
        $tags = Tag::whereHas('products', function ($query) use ($productType) {
            $query->where('product_type_id', $productType->id);
        })->get();

        $crumbs = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => $productType->name],
        ];

        return view('product-type', compact('productType', 'products', 'categories', 'colors', 'tags', 'crumbs'));
    }
}
