@extends('layouts.app')

@section('title', $productType->meta_title ?? $productType->name)
@section('meta_description', $productType->meta_description)
@section('meta_keywords', $productType->meta_keywords)

@section('meta_tags')
    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="@yield('meta_description')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    @if($productType->image)
        <meta property="og:image" content="{{ asset('storage/' . $productType->image) }}">
    @endif
@endsection

@section('content')
    <x-breadcrumb :title="$productType->name" :crumbs="$crumbs" />

    <!-- MAIN CONTENT SECTION START -->
    <div class="ul-inner-page-container">
        <div class="ul-inner-products-wrapper">
            <div class="row ul-bs-row flex-column-reverse flex-md-row">
                <x-shop-sidebar :categories="$categories" :colors="$colors" :tags="$tags" />

                <!-- right products container -->
                <div class="col-lg-9 col-md-8">
                    <div class="row ul-bs-row row-cols-lg-3 row-cols-2 row-cols-xxs-1">
                        @foreach ($products as $product)
                            <!-- product card -->
                            <div class="col">
                                <div class="ul-product">
                                    <div class="ul-product-heading">
                                        <span class="ul-product-price">${{ number_format($product->price, 0, ',', '.') }}</span>
                                        @if($product->getDiscountPercentageAttribute() > 0)
                                            <span class="ul-product-discount-tag">-{{ $product->getDiscountPercentageAttribute() }}% Dto</span>
                                        @endif
                                    </div>

                                    <div class="ul-product-img">
                                        <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}">


                                    </div>

                                    <div class="ul-product-txt">
                                        <h4 class="ul-product-title"><a href="{{ route('product.show', $product) }}">{{ $product->name }}</a></h4>
                                        <h5 class="ul-product-category"><a href="{{ route('category.show', ['productType' => $product->category->productType->slug, 'category' => $product->category->slug]) }}">{{ $product->category->name }}</a></h5>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- pagination -->
                    <div class="ul-pagination">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- MAIN CONTENT SECTION END -->
@endsection
