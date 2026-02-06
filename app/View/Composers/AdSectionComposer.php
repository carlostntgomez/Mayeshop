<?php

namespace App\View\Composers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class AdSectionComposer
{
    public function compose(View $view)
    {
        // Find the maximum discount percentage
        $maxDiscount = Product::whereNotNull('sale_price')
            ->where('price', '>', 0)
            ->select(DB::raw('MAX((price - sale_price) / price * 100) as max_discount'))
            ->value('max_discount');

        // Find top 4 categories with the most discounted products
        $topCategories = Category::select('categories.id', 'categories.name', 'categories.slug', DB::raw('count(products.id) as discounted_products_count'))
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->whereNotNull('products.sale_price')
            ->where('products.price', '>', 'products.sale_price')
            ->groupBy('categories.id', 'categories.name', 'categories.slug')
            ->orderByDesc('discounted_products_count')
            ->take(4)
            ->get();

        $view->with([
            'maxDiscount' => floor($maxDiscount),
            'topCategories' => $topCategories,
        ]);
    }
}
