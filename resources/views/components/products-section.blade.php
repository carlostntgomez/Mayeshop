<div class="ul-container">
    <section class="ul-products">
        <div class="ul-inner-container">
            <div class="ul-section-heading">
                <div class="left">
                    <span class="ul-section-sub-title">{{ $featuredProductsSection->subtitle ?? '' }}</span>
                    <h2 class="ul-section-title">{{ $featuredProductsSection->title ?? '' }}</h2>
                </div>

                <div class="right"><a href="{{ $featuredProductsSection->button_url ?? '#' }}" class="ul-btn">{{ $featuredProductsSection->button_text ?? '' }} <i class="fas fa-arrow-up-right-from-square"></i></a></div>
            </div>


            <div class="row ul-bs-row">
                <!-- 1st row -->
                <div class="col-lg-3 col-md-4 col-12">
                    <!-- sub bannner -->
                    @if ($featuredProductsSection && isset($featuredProductsSection->sub_banners_data[0]))
                        @php
                            $subBanner = (object) $featuredProductsSection->sub_banners_data[0];
                        @endphp
                        <div class="ul-products-sub-banner">
                            <div class="ul-products-sub-banner-img">
                                <img src="{{ asset('storage/' . $subBanner->image_path) }}" alt="Sub Banner Image">
                            </div>
                            <div class="ul-products-sub-banner-txt">
                                <h3 class="ul-products-sub-banner-title">{{ $subBanner->title }}</h3>
                                <a href="{{ $subBanner->button_url }}" class="ul-btn">{{ $subBanner->button_text }} <i class="fas fa-arrow-up-right-from-square"></i></a>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-lg-9 col-md-8 col-12">
                    <!-- products grid -->
                    <div class="swiper ul-products-slider-1">
                        <div class="swiper-wrapper">
                            @if ($featuredProductsSection && isset($featuredProductsSection->product_grids_data[0]))
                                @foreach ($featuredProductsSection->product_grids_data[0]['products'] as $productId)
                                    @php
                                        $product = $products->firstWhere('id', $productId);
                                    @endphp
                                    @if ($product)
                                        <!-- product card -->
                                        <div class="swiper-slide">
                                            <div class="ul-product">
                                                <div class="ul-product-heading">
                                                    @if ($product->sale_price && $product->sale_price < $product->price)
                                                        <span class="ul-product-price">${{ number_format($product->sale_price, 0, ',', '.') }}</span>
                                                    @else
                                                        <span class="ul-product-price">${{ number_format($product->price, 0, ',', '.') }}</span>
                                                    @endif
                                                    @if($product->getDiscountPercentageAttribute() > 0)
                                                        <span class="ul-product-discount-tag" data-tooltip="Ahorras ${{ number_format($product->price - $product->sale_price, 0) }}">-{{ $product->getDiscountPercentageAttribute() }}% Dto</span>
                                                    @endif
                                                </div>

                                                <div class="ul-product-img">
                                                    <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}">
                                                </div>

                                                <div class="ul-product-txt">
                                                    <h4 class="ul-product-title"><a href="{{ route('product.show', $product) }}">{{ $product->name }}</a></h4>
                                                    <h5 class="ul-product-category">
                                                        @if ($product->category && $product->category->slug && $product->category->productType && $product->category->productType->slug)
                                                            <!-- ProductType Slug: {{ $product->category->productType->slug }} -->
                                                            <!-- Category Slug: {{ $product->category->slug }} -->
                                                            <a href="{{ route('category.show', ['productType' => $product->category->productType->slug, 'category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
                                                        @else
                                                            {{ $product->category->name ?? 'Sin Categoría' }}
                                                        @endif
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- slider navigation -->
                    <div class="ul-products-slider-nav ul-products-slider-1-nav">
                        <button class="prev"><i class="fas fa-arrow-left"></i></button>
                        <button class="next"><i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                <!-- 2nd row -->
                <div class="col-lg-3 col-md-4 col-12">
                    <!-- sub bannner -->
                    @if ($featuredProductsSection && isset($featuredProductsSection->sub_banners_data[1]))
                        @php
                            $subBanner = (object) $featuredProductsSection->sub_banners_data[1];
                        @endphp
                        <div class="ul-products-sub-banner">
                            <div class="ul-products-sub-banner-img">
                                <img src="{{ asset('storage/' . $subBanner->image_path) }}" alt="Sub Banner Image">
                            </div>
                            <div class="ul-products-sub-banner-txt">
                                <h3 class="ul-products-sub-banner-title">{{ $subBanner->title }}</h3>
                                <a href="{{ $subBanner->button_url }}" class="ul-btn">{{ $subBanner->button_text }} <i class="fas fa-arrow-up-right-from-square"></i></a>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-lg-9 col-md-8 col-12">
                    <!-- products grid -->
                    <div class="swiper ul-products-slider-2">
                        <div class="swiper-wrapper">
                            @if ($featuredProductsSection && isset($featuredProductsSection->product_grids_data[1]))
                                @foreach ($featuredProductsSection->product_grids_data[1]['products'] as $productId)
                                    @php
                                        $product = $products->firstWhere('id', $productId);
                                    @endphp
                                    @if ($product)
                                        <!-- product card -->
                                        <div class="swiper-slide">
                                            <div class="ul-product">
                                                <div class="ul-product-heading">
                                                    @if ($product->sale_price && $product->sale_price < $product->price)
                                                        <span class="ul-product-price">${{ number_format($product->sale_price, 0, ',', '.') }}</span>
                                                    @else
                                                        <span class="ul-product-price">${{ number_format($product->price, 0, ',', '.') }}</span>
                                                    @endif
                                                    @if($product->getDiscountPercentageAttribute() > 0)
                                                        <span class="ul-product-discount-tag" data-tooltip="Ahorras ${{ number_format($product->price - $product->sale_price, 0) }}">-{{ $product->getDiscountPercentageAttribute() }}% Dto</span>
                                                    @endif
                                                </div>

                                                <div class="ul-product-img">
                                                    <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}">
                                                </div>

                                                <div class="ul-product-txt">
                                                    <h4 class="ul-product-title"><a href="{{ route('product.show', $product) }}">{{ $product->name }}</a></h4>
                                                    <h5 class="ul-product-category">
                                                        @if ($product->category && $product->category->slug && $product->category->productType && $product->category->productType->slug)
                                                            <a href="{{ route('category.show', ['productType' => $product->category->productType->slug, 'category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
                                                        @else
                                                            {{ $product->category->name ?? 'Sin Categoría' }}
                                                        @endif
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- slider navigation -->
                    <div class="ul-products-slider-nav ul-products-slider-2-nav">
                        <button class="prev"><i class="fas fa-arrow-left"></i></button>
                        <button class="next"><i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>
        </section>
    </div>