<?php

namespace App\View\Composers;

use App\Models\Product;
use App\Models\SocialMediaLink;
use Illuminate\View\View;

class SidebarComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $latestProducts = Product::with(['category.productType'])->latest()->take(5)->get();
        $socialMediaLinks = SocialMediaLink::where('is_active', true)->orderBy('order')->get();
        
        $view->with(compact('latestProducts', 'socialMediaLinks'));
    }
}