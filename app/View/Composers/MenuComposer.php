<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\ProductType;
use App\Models\BlogCategory;
use App\Models\HeaderAnnouncement;

class MenuComposer
{
    public function compose(View $view)
    {
        $menuProductTypes = ProductType::whereHas('products')
            ->with(['categories' => function ($query) {
                $query->whereHas('products');
            }])
            ->get()
            ->groupBy('gender');
        $blogCategories = BlogCategory::all();
        $headerAnnouncements = HeaderAnnouncement::where('is_active', true)->orderBy('order')->get();

        $view->with(compact('menuProductTypes', 'blogCategories', 'headerAnnouncements'));
    }
}
