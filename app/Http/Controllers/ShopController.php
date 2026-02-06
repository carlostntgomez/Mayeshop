<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query();

        if ($request->has('search')) {
            $products->where('name', 'like', '%' . $request->search . '%')
                     ->orWhere('short_description', 'like', '%' . $request->search . '%');
        }

        if ($request->boolean('on_sale')) {
            $products->whereNotNull('sale_price')->where('price', '>', DB::raw('sale_price'));
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
        
        // Solo categorías con productos
        $categories = Category::whereHas('products')->get();

        // Solo colores asociados a productos
        $colors = Color::whereHas('products')->get();

        // Solo etiquetas con productos
        $tags = Tag::whereHas('products')->get();

        $crumbs = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Tienda'],
        ];

        return view('shop', compact('products', 'categories', 'colors', 'tags', 'crumbs'));
    }
}
