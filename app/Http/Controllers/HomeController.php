<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\GalleryImage;
use App\Models\Post;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\HomepageSubBanner;
use App\Models\FeaturedProductsSection;
use App\Models\ProductType;
use App\Models\FlashSale;
use App\Models\Video;
use App\Models\LegalPage;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('is_active', true)->orderBy('order')->get();
        $categories = Category::with('productType')->inRandomOrder()->take(6)->get();

        $posts = Post::latest()->take(9)->get();
        $homepageSubBanners = HomepageSubBanner::all();

        $featuredProductsSection = FeaturedProductsSection::first();
        $featuredProducts = collect();

        if ($featuredProductsSection && is_array($featuredProductsSection->product_grids_data)) {
            $productIds = collect($featuredProductsSection->product_grids_data)
                            ->flatMap(function ($grid) {
                                return $grid['products'];
                            })
                            ->toArray();
            $featuredProducts = Product::with(['category.productType'])->whereIn('id', $productIds)->get();
        }

        $productTypesForFilters = ProductType::all();
        $latestProducts = Product::with(['category.productType'])->latest()->take(20)->get();

        $mostSellingProducts = Product::where('is_most_selling', true)->get();

        // Obtener productos en venta flash
        $flashSaleProducts = collect();
        $activeFlashSales = FlashSale::where('is_active', true)
                                    ->where('start_date', '<=', now())
                                    ->where('end_date', '>=', now())
                                    ->with('products')
                                    ->get();

        foreach ($activeFlashSales as $flashSale) {
            $flashSaleProducts = $flashSaleProducts->merge($flashSale->products);
        }

        // Obtener reseñas y cargar la relación 'product'
        $reviews = Review::with(['product'])->get();

        $galleryImages = GalleryImage::all();

        $video = Video::where('is_active', true)->first();

        $menuProductTypes = ProductType::with('categories')->get()->groupBy('gender');

        return view('home', compact('banners', 'categories', 'featuredProducts', 'mostSellingProducts', 'flashSaleProducts', 'reviews', 'galleryImages', 'posts', 'homepageSubBanners', 'featuredProductsSection', 'productTypesForFilters', 'latestProducts', 'video', 'menuProductTypes'));
    }

    public function showBlogDetails(BlogCategory $blogCategory, Post $post)
    {
        $recentPosts = Post::where('id', '!=', $post->id)->latest()->take(3)->get();
        $blogCategories = BlogCategory::has('posts')->get();
        $blogTags = BlogTag::has('posts')->get();

        $crumbs = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Blog', 'url' => route('blog.index')],
            ['name' => $blogCategory->name, 'url' => route('blog.category.show', $blogCategory)],
            ['name' => $post->title],
        ];

        return view('blog-details', compact('post', 'recentPosts', 'blogCategories', 'blogTags', 'crumbs'));
    }

    public function indexBlog()
    {
        $posts = Post::latest()->paginate(9);
        $recentPosts = Post::latest()->take(3)->get();
        $blogCategories = BlogCategory::has('posts')->get();
        $blogTags = BlogTag::has('posts')->get();

        $crumbs = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Blog'],
        ];

        return view('blog-grid', compact('posts', 'recentPosts', 'blogCategories', 'blogTags', 'crumbs'));
    }

    public function showBlogCategory(BlogCategory $blogCategory)
    {
        $posts = $blogCategory->posts()->paginate(9);
        $recentPosts = Post::latest()->take(3)->get();
        $blogCategories = BlogCategory::has('posts')->get();
        $blogTags = BlogTag::has('posts')->get();

        $crumbs = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Blog', 'url' => route('blog.index')],
            ['name' => $blogCategory->name],
        ];

        return view('blog-category', compact('blogCategory', 'posts', 'recentPosts', 'blogCategories', 'blogTags', 'crumbs'));
    }

    public function showBlogTag(BlogTag $tag)
    {
        $posts = $tag->posts()->paginate(9);
        $recentPosts = Post::latest()->take(3)->get();
        $blogCategories = BlogCategory::has('posts')->get();
        $blogTags = BlogTag::has('posts')->get();

        $crumbs = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Blog', 'url' => route('blog.index')],
            ['name' => $tag->name],
        ];

        return view('blog-tag', compact('tag', 'posts', 'recentPosts', 'blogCategories', 'blogTags', 'crumbs'));
    }

    public function showAboutPage()
    {
        $aboutContent = \App\Models\AboutPageContent::first();
        $reviews = Review::with(['product'])->get();
        $crumbs = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Acerca de'],
        ];
        return view('about', compact('aboutContent', 'reviews', 'crumbs'));
    }

    public function showContactPage()
    {
        return view('contact');
    }

    public function showFaqPage()
    {
        return view('faq');
    }

    public function showCartPage()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'], // Use the price stored in the session for consistency
                    'subtotal' => $item['quantity'] * $item['price']
                ];
                $subtotal += $item['quantity'] * $item['price'];
            }
        }

        // Shipping cost is 0.00 as per user's request
        $shippingCost = 0.00;

        $total = $subtotal + $shippingCost;

        $crumbs = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Carrito'],
        ];

        return view('cart', compact('cartItems', 'subtotal', 'shippingCost', 'total', 'crumbs'));
    }

    public function showCheckoutPage()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['quantity'] * $item['price']
                ];
                $subtotal += $item['quantity'] * $item['price'];
            }
        }

        $shippingCost = 0.00; // Shipping is free

        $total = $subtotal + $shippingCost;

        $crumbs = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Checkout'],
        ];

        return view('checkout', compact('cartItems', 'subtotal', 'shippingCost', 'total', 'crumbs'));
    }

    public function showTermsPage()
    {
        $legalPage = LegalPage::where('type', 'terms')->firstOrFail();
        $crumbs = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => $legalPage->title],
        ];
        return view('terms', compact('crumbs', 'legalPage'));
    }

    public function showPrivacyPage()
    {
        $legalPage = LegalPage::where('type', 'privacy')->firstOrFail();
        $crumbs = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => $legalPage->title],
        ];
        return view('privacy', compact('crumbs', 'legalPage'));
    }

    public function showRefundPage()
    {
        $legalPage = LegalPage::where('type', 'refund')->firstOrFail();
        $crumbs = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => $legalPage->title],
        ];
        return view('refund', compact('crumbs', 'legalPage'));
    }
}
