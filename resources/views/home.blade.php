@extends('layouts.app')

@section('title', 'Inicio - Maye Shop | Tu Tienda de Moda Online')

@section('meta_description', 'Descubre las últimas tendencias en moda en Maye Shop. Encuentra vestidos, ropa y accesorios de lujo para cada ocasión. Calidad y estilo en un solo lugar.')
@section('meta_keywords', 'Maye Shop, tienda de moda, ropa de mujer, vestidos, tendencias de moda, lujo accesible, tienda online')

@section('meta_tags')
    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="@yield('meta_description')">
    <meta property="og:url" content="{{ route('home') }}">
    <meta property="og:type" content="website">
@endsection

@section('content')
    <x-banner-section :banners="$banners" />

    <x-category-section :categories="$categories" />

    <x-products-section :products="$featuredProducts" :featuredProductsSection="$featuredProductsSection" />

    <x-ad-section />

    <x-product-type-section :productTypesForFilters="$productTypesForFilters" :latestProducts="$latestProducts" />


        <x-video-section :video="$video" />


        <x-sub-banner-section :homepageSubBanners="$homepageSubBanners" />


    <x-flash-sale-section :flashSaleProducts="$flashSaleProducts" />


        <x-reviews-section :reviews="$reviews" />


        <x-newsletter-subscription-section />


        <x-blog-section :posts="$posts" />    <!-- BLOG SECTION END -->


        <x-gallery-section :galleryImages="$galleryImages" />
@endsection

@push('scripts')
    <script src="{{ asset('static/js/countdown.js') }}"></script>
@endpush