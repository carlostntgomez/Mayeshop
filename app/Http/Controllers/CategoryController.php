<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ProductType;
use Illuminate\Http\Request;
use App\Models\Color;
use App\Models\Tag;

class CategoryController extends Controller
{
    public function show(Request $request, ProductType $productType, Category $category)
    {
        $products = $category->products();

        if ($request->has('search')) {
            $products->where('products.name', 'like', '%' . $request->search . '%')
                     ->orWhere('products.short_description', 'like', '%' . $request->search . '%');
        }



        if ($request->has('tag')) {
            $products->whereHas('tags', function ($query) use ($request) {
                $query->where('slug', $request->tag);
            });
        }

        if ($request->has('color')) {
            $products->whereHas('colors', function ($query) use ($request) {
                $query->where('name', $request->color);
            });
        }



        $products = $products->paginate(24); // Mostrar 24 productos por página

        $colors = Color::whereHas('products', function ($query) use ($category) {
            $query->where('category_id', $category->id);
        })->get();
        $tags = Tag::whereHas('products', function ($query) use ($category) {
            $query->where('category_id', $category->id);
        })->get();

        $categories = collect(); // Empty collection for the sidebar

        $crumbs = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => $productType->name, 'url' => route('product_type.show', $productType->slug)],
            ['name' => $category->name],
        ];

        return view('categories.show', compact('productType', 'category', 'products', 'categories', 'colors', 'tags', 'crumbs'));
    }
}

