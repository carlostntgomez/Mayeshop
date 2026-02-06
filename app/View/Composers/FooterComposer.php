<?php

namespace App\View\Composers;

use App\Models\SocialMediaLink;
use App\Models\ProductType;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\View\View;

class FooterComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $socialMediaLinks = SocialMediaLink::where('is_active', true)->orderBy('order')->get();
        $footerProductTypes = ProductType::take(5)->get();
        $footerCategories = Category::take(5)->get();
        $whatsappPhoneNumber = Setting::where('key', 'whatsapp_phone_number')->first()?->value;

        $view->with(compact('socialMediaLinks', 'footerProductTypes', 'footerCategories', 'whatsappPhoneNumber'));
    }
}
