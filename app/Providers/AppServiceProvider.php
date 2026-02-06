<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\View\Composers\MenuComposer;
use App\View\Composers\CartComposer; // Add this line
use App\View\Composers\SidebarComposer;
use App\View\Composers\FooterComposer;
use App\View\Composers\AdSectionComposer;
use App\View\Composers\ThemeColorComposer;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.ul-pagination');

        View::composer('components.header', MenuComposer::class);
        View::composer('components.header', CartComposer::class);
        View::composer('components.sidebar', SidebarComposer::class);
        View::composer('components.footer', FooterComposer::class);
        View::composer('components.ad-section', AdSectionComposer::class);
        View::composer('layouts.app', ThemeColorComposer::class);

    }
}
