<?php

namespace App\View\Composers;

use Illuminate\View\View;

class CartComposer
{
    public function compose(View $view)
    {
        $cart = session()->get('cart', []);
        $cartCount = array_sum(array_column($cart, 'quantity'));
        $view->with('cartCount', $cartCount);
    }
}
