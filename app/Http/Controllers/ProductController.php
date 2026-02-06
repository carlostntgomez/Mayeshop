<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SocialMediaLink;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($slug)
    {
        $product = Product::where('slug', $slug)->with(['reviews', 'colors', 'tags', 'occasions', 'estilos', 'temporadas', 'materials', 'productType', 'category'])->firstOrFail();
        $socialMediaLinks = SocialMediaLink::where('is_active', true)->orderBy('order')->get();

        $crumbs = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => $product->productType->name, 'url' => route('product_type.show', $product->productType->slug)],
            ['name' => $product->category->name, 'url' => route('category.show', ['productType' => $product->productType->slug, 'category' => $product->category->slug])],
            ['name' => $product->name],
        ];

        return view('products.show', compact('product', 'socialMediaLinks', 'crumbs'));
    }
}
