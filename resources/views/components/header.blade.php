<!-- HEADER SECTION START -->
    <header class="ul-header">
        <!-- header top -->
        <div class="ul-header-top">
            <div class="ul-header-top-slider splide">
                <div class="splide__track">
                    <ul class="splide__list">
                        @foreach ($headerAnnouncements as $announcement)
                            <li class="splide__slide">
                                @if ($announcement->url)
                                    <a href="{{ $announcement->url }}" class="ul-header-top-slider-item">
                                @else
                                    <p class="ul-header-top-slider-item">
                                @endif
                                <i class="{{ $announcement->icon ?? 'fas fa-star' }}"></i> {{ $announcement->text }}
                                @if ($announcement->url)
                                    </a>
                                @else
                                    </p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- header bottom -->
        <div class="ul-header-bottom">
            <div class="ul-container">
                <div class="ul-header-bottom-wrapper">
                    <!-- header left -->
                    <div class="header-bottom-left">
                        <div class="logo-container">
                            <a href="{{ route('home') }}" class="d-inline-block"><img src="{{ asset('static/picture/logo.svg') }}" alt="logo" class="logo"></a>
                        </div>

                        <!-- search form -->
                        <div class="ul-header-search-form-wrapper flex-grow-1 flex-shrink-0">
                            <form action="{{ route('shop.index') }}" method="GET" class="ul-header-search-form">

                                <div class="ul-header-search-form-right">
                                    <input type="search" name="search" id="ul-header-search" placeholder="Buscar aquí">
                                    <button type="submit"><i class="fas fa-search"></i></span></button>
                                </div>
                            </form>

                            <button class="ul-header-mobile-search-closer d-xxl-none"><i class="fas fa-times"></i></button>
                        </div>
                    </div>

                    <!-- header nav -->
                    <div class="ul-header-nav-wrapper">
                        <div class="to-go-to-sidebar-in-mobile">
                            <nav class="ul-header-nav">
                                <a href="{{ route('home') }}">Inicio</a>
                                <a href="{{ route('shop.index') }}">Tienda</a>

                                @foreach ($menuProductTypes as $gender => $productTypes)
                                    <div class="has-sub-menu">
                                        <a role="button" href="#">{{ ucfirst($gender) }}</a>

                                        <ul class="ul-header-submenu">
                                            @foreach ($productTypes as $productType)
                                                <li @if ($productType->categories->isNotEmpty()) class="has-submenu-level2" @endif>
                                                    <a href="{{ route('product_type.show', $productType->slug) }}" class="d-flex justify-content-between align-items-center">
                                                        {{ $productType->name }}
                                                        @if ($productType->categories->isNotEmpty())
                                                            <i class="fas fa-angle-right ms-2"></i>
                                                        @endif
                                                    </a>
                                                    @if ($productType->categories->isNotEmpty())
                                                        <ul class="ul-header-submenu-level2">
                                                            @foreach ($productType->categories as $category)
                                                                <li><a href="{{ route('category.show', ['productType' => $productType->slug, 'category' => $category->slug]) }}">{{ $category->name }}</a></li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach

                                <div class="has-sub-menu">
                                    <a href="{{ route('blog.index') }}">Blog</a>
                                    <div class="ul-header-submenu">
                                        <ul>
                                            @foreach ($blogCategories as $blogCategory)
                                                <li><a href="{{ route('blog.category.show', $blogCategory) }}">{{ $blogCategory->name }}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                <div class="has-sub-menu">
                                    <a role="button">Información</a>
                                    <div class="ul-header-submenu">
                                        <ul>
                                            <li><a href="{{ route('about') }}">Acerca de</a></li>
                                            <li><a href="{{ route('contact') }}">Contacto</a></li>
                                            <li><a href="{{ route('faq') }}">Preguntas Frecuentes</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </nav>
                        </div>
                    </div>

                    <!-- actions -->
                    <div class="ul-header-actions">
                        <button class="ul-header-mobile-search-opener d-xxl-none"><i class="fas fa-search"></i></button>
                        <a href="{{ route('cart.index') }}"><i class="fas fa-shopping-bag"></i> <span id="cart-count" class="badge bg-danger rounded-circle @if($cartCount == 0) d-none @endif">{{ $cartCount }}</span></a>
                    </div>

                    <!-- sidebar opener -->
                    <div class="d-inline-flex">
                        <button class="ul-header-sidebar-opener"><i class="fas fa-bars"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- HEADER SECTION END -->