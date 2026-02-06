<div class="overflow-hidden">
    <div class="ul-container">
        <div class="ul-flash-sale">
            <div class="ul-inner-container">
                <!-- heading -->
                <div class="ul-section-heading ul-flash-sale-heading">
                    <div class="left">
                        <span class="ul-section-sub-title">Nueva Colección</span>
                        <h2 class="ul-section-title">Ofertas Flash del Momento</h2>
                    </div>

                    <div class="ul-flash-sale-countdown-wrapper">
                        <div class="ul-flash-sale-countdown">
                            <div class="days-wrapper">
                                <div class="days number">00</div>
                                <span class="txt">Días</span>
                            </div>
                            <div class="hours-wrapper">
                                <div class="hours number">00</div>
                                <span class="txt">Horas</span>
                            </div>
                            <div class="minutes-wrapper">
                                <div class="minutes number">00</div>
                                <span class="txt">Min</span>
                            </div>
                            <div class="seconds-wrapper">
                                <div class="seconds number">00</div>
                                <span class="txt">Seg</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('shop.index', ['on_sale' => true]) }}" class="ul-btn">Ver Toda la Colección <i class="fas fa-arrow-up-right-from-square"></i></a>
                </div>

                <!-- produtcs slider -->
                <div class="ul-flash-sale-slider swiper overflow-visible">
                    <div class="swiper-wrapper">
                        @foreach ($flashSaleProducts as $product)
                            <!-- single product -->
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
                                        <h5 class="ul-product-category"><a href="{{ route('category.show', ['productType' => $product->category->productType->slug, 'category' => $product->category->slug]) }}">{{ $product->category->name }}</a></h5>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>