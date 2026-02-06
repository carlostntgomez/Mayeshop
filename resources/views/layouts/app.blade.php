<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- Added for AJAX requests --}}
    <title>@yield('title', 'Maye - Tu Tienda Online')</title>
    <meta name="description" content="@yield('meta_description', 'Tu tienda de moda online con las últimas tendencias.')">
    <meta name="keywords" content="@yield('meta_keywords', 'moda, ropa, vestidos, tendencias, tienda online')">
    @yield('meta_tags')

    <!-- libraries CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/static/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/css/splide.min.css">
    <link rel="stylesheet" href="/static/css/swiper-bundle.min.css">
    <link rel="stylesheet" href="/static/css/slimselect.css">
    <link rel="stylesheet" href="/static/css/animate.min.css">

    <!-- custom CSS -->
    <link rel="stylesheet" href="/static/css/style.css">
    @livewireStyles
                @stack('captcha-scripts')
    <style>
        :root {
            @foreach ($themeColors as $name => $value)
                {{ $name }}: {{ $value }};
            @endforeach
        }
    </style>
</head>
<body>
    <div class="preloader" id="preloader">
        <div class="loader"></div>
    </div>

    <x-sidebar />

            <x-header />
    
            <div class="ul-container mt-4">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
    
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
    
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
    
            @yield('content')
    <x-footer />

    <!-- libraries JS -->
    <script src="{{ asset('static/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('static/js/splide.min.js') }}"></script>
    <script src="{{ asset('static/js/splide-extension-auto-scroll.min.js') }}"></script>
    <script src="{{ asset('static/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('static/js/slimselect.min.js') }}"></script>
    <script src="{{ asset('static/js/wow.min.js') }}"></script>
    <script src="{{ asset('static/js/index.min.js') }}"></script>
    <script src="{{ asset('static/js/mixitup.min.js') }}"></script>
    <script src="{{ asset('static/js/fslightbox.js') }}"></script>

    <!-- custom JS -->
    @stack('head_scripts')
    <script src="{{ asset('static/js/main.js') }}"></script>
    <script src="{{ asset('static/js/accordion.js') }}"></script>

    @stack('scripts')
    @livewireScripts

    <!-- Cart Confirmation Modal -->
    <div class="modal fade" id="cartConfirmationModal" tabindex="-1" aria-labelledby="cartConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ul-cart-modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartConfirmationModalLabel"></h5>
                    <button type="button" class="ul-modal-close-btn" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body" id="cartConfirmationModalBody">
                    <!-- Message will be inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="ul-btn ul-btn--secondary" data-bs-dismiss="modal">Continuar Comprando</button>
                    <a href="{{ route('cart.index') }}" class="ul-btn">Ver Carrito</a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .ul-cart-modal-content {
            border-radius: 20px;
            border: 1px solid var(--ul-primary);
            background-color: #fff;
        }
        .ul-cart-modal-content .modal-header {
            border-bottom: 1px solid #eee;
            padding: 1.5rem;
        }
        .ul-cart-modal-content .modal-header .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--ul-primary);
        }
        .ul-cart-modal-content .modal-body {
            padding: 1.5rem;
            font-size: 1.1rem;
        }
        .ul-cart-modal-content .modal-footer {
            border-top: 1px solid #eee;
            padding: 1.5rem;
            gap: 1rem;
        }
        .ul-modal-close-btn {
            background: transparent;
            border: none;
            font-size: 1.5rem;
            color: var(--black);
        }
        .ul-modal-close-btn:hover {
            color: var(--ul-primary);
        }
        .ul-btn--secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
        }
        .ul-btn--secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
    </style>

    </body>
</html>