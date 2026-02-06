    <div class="ul-sidebar">
        <!-- header -->
        <div class="ul-sidebar-header">
            <div class="ul-sidebar-header-logo">
                <a href="">
                    <img src="{{ asset('static/picture/logo.svg') }}" alt="logo" class="logo">
                </a>
            </div>
            <!-- sidebar closer -->
            <button class="ul-sidebar-closer"><i class="fas fa-times"></i></button>
        </div>

        <div class="ul-sidebar-header-nav-wrapper d-block d-lg-none"></div>

        <div class="ul-sidebar-about d-none d-lg-block">
            <span class="title">Acerca de Maye Shop</span>
            <p class="mb-0">Maye Shop es tu destino ideal para encontrar las últimas tendencias en moda. Ofrecemos una cuidada selección de prendas y accesorios para que siempre luzcas a la vanguardia. Nuestra misión es brindarte productos de calidad con un estilo único y a precios accesibles. Explora nuestras colecciones y descubre tu próximo look favorito. ¡Gracias por elegir Maye Shop!</p>
        </div>


        <!-- product slider -->
        <div class="ul-sidebar-products-wrapper d-none d-lg-flex">
            <div class="ul-sidebar-products-slider swiper">
                <div class="swiper-wrapper">
                    @foreach ($latestProducts as $product)
                    <!-- product card -->
                    <div class="swiper-slide">
                        <div class="ul-product">
                            <div class="ul-product-heading">
                                <span class="ul-product-price">${{ number_format($product->sale_price ?? $product->price, 0) }}</span>
                            </div>

                            <div class="ul-product-img">
                                <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}">


                            </div>

                            <div class="ul-product-txt">
                                <h4 class="ul-product-title"><a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a></h4>
                                @if ($product->category && $product->category->productType)
                                <h5 class="ul-product-category"><a href="{{ route('category.show', ['productType' => $product->category->productType->slug, 'category' => $product->category->slug]) }}">{{ $product->category->name }}</a></h5>
                                @else
                                <h5 class="ul-product-category">{{ $product->category->name ?? 'Sin Categoría' }}</h5>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="ul-sidebar-products-slider-nav flex-shrink-0">
                <button class="prev"><i class="fas fa-arrow-left"></i></button>
                <button class="next"><i class="fas fa-arrow-right"></i></button>
            </div>
        </div>

        <!-- sidebar footer -->
        <div class="ul-sidebar-footer">
            <span class="ul-sidebar-footer-title">Síguenos</span>

            <div class="ul-sidebar-footer-social">
                @foreach ($socialMediaLinks as $socialMediaLink)
                    <a href="{{ $socialMediaLink->url }}" target="_blank"><i class="{{ $socialMediaLink->icon }}"></i></a>
                @endforeach
            </div>
        </div>
    </div>