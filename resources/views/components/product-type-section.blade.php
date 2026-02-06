<div class="ul-container">
    <section class="ul-products ul-most-selling-products">
        <div class="ul-inner-container">
            <div class="ul-section-heading flex-lg-row flex-column text-md-start text-center">
                <div class="left">
                    <span class="ul-section-sub-title">Productos Destacados por Tipo</span>
                    <h2 class="ul-section-title">Tipos de Producto</h2>
                </div>

                <div class="right">
                    <div class="ul-most-sell-filter-navs">
                        <button type="button" data-filter="all">Todos</button>
                        @foreach ($productTypesForFilters as $productType)
                            <button type="button" data-filter=".{{ Str::slug($productType->name) }}">{{ $productType->name }}</button>
                        @endforeach
                    </div>
                </div>
            </div>


            <!-- products grid -->
            <div class="ul-bs-row row row-cols-xl-4 row-cols-lg-3 row-cols-sm-2 row-cols-1 ul-filter-products-wrapper">
                @foreach ($latestProducts as $product)
                    @if ($product->productType)
                        <!-- product card -->
                        <div class="mix col {{ Str::slug($product->productType->name) }}">
                            <div class="ul-product-horizontal">
                                <div class="ul-product-horizontal-img">
                                    <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}">
                                </div>

                                <div class="ul-product-horizontal-txt">
                                    @if ($product->sale_price && $product->sale_price < $product->price)
                                        <span class="ul-product-price">${{ number_format($product->sale_price, 0, ',', '.') }}</span>
                                    @else
                                        <span class="ul-product-price">${{ number_format($product->price, 0, ',', '.') }}</span>
                                    @endif
                                    @if($product->getDiscountPercentageAttribute() > 0)
                                        <span class="ul-product-discount-tag" data-tooltip="Ahorras ${{ number_format($product->price - $product->sale_price, 0) }}">-{{ $product->getDiscountPercentageAttribute() }}% Dto</span>
                                    @endif
                                    <h4 class="ul-product-title"><a href="{{ route('product.show', $product) }}">{{ $product->name }}</a></h4>
                                    <h5 class="ul-product-category">
                                        @if ($product->category && $product->category->slug && $product->category->productType && $product->category->productType->slug)
                                            <a href="{{ route('category.show', ['productType' => $product->category->productType->slug, 'category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
                                        @else
                                            @php
                                                // Fallback or error handling if category or productType is missing
                                                // For now, just display product name if category is missing
                                                echo $product->name;
                                            @endphp
                                        @endif
                                    </h5>
                                    <div class="ul-product-rating">
                                        @for ($i = 0; $i < 5; $i++)
                                            @if ($i < $product->getAverageRatingAttribute())
                                                <span class="star"><i class="fas fa-star"></i></span>
                                            @else
                                                <span class="star"><i class="far fa-star"></i></span>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
</div>