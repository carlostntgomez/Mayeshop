@extends('layouts.app')

@push('captcha-scripts')
    {!! NoCaptcha::renderJs() !!}
@endpush

@section('title', $product->meta_title ?? $product->name)
@section('meta_description', $product->meta_description)
@section('meta_keywords', $product->meta_keywords)

@section('meta_tags')
    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="@yield('meta_description')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="product">
    @if($product->main_image)
        <meta property="og:image" content="{{ asset('storage/' . $product->main_image) }}">
    @endif
@endsection

@section('content')
        <x-breadcrumb :title="$product->name" :crumbs="$crumbs" />


        <!-- MAIN CONTENT SECTION START -->
        <div class="ul-inner-page-container">
            <div class="ul-product-details">
                <div class="ul-product-details-top">
                    <div class="row ul-bs-row row-cols-lg-2 row-cols-1 align-items-center">
                        <!-- img -->
                        <div class="col">
                            <div class="ul-product-details-img">
                                <div class="ul-product-details-img-slider swiper">
                                    <div class="swiper-wrapper">
                                        <!-- main img -->
                                        <div class="swiper-slide"><img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}"></div>
                                        @php
                                            $galleryImages = [];
                                            if (!empty($product->image_gallery)) {
                                                if (is_string($product->image_gallery)) {
                                                    $decoded = json_decode($product->image_gallery, true);
                                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                        $galleryImages = $decoded;
                                                    } else {
                                                        // Assume it's a comma-separated string or single path
                                                        $galleryImages = explode(',', $product->image_gallery);
                                                        $galleryImages = array_map('trim', $galleryImages); // Trim whitespace
                                                        $galleryImages = array_filter($galleryImages); // Remove empty elements
                                                    }
                                                } elseif (is_array($product->image_gallery)) {
                                                    $galleryImages = $product->image_gallery;
                                                }
                                            }
                                        @endphp
                                        @foreach ($galleryImages as $imagePath)
                                            <!-- gallery img -->
                                            <div class="swiper-slide"><img src="{{ asset('storage/' . $imagePath) }}" alt="{{ $product->name }}"></div>
                                        @endforeach
                                    </div>

                                    <div class="ul-product-details-img-slider-nav" id="ul-product-details-img-slider-nav">
                                        <button class="prev"><i class="fas fa-arrow-left"></i></button>
                                        <button class="next"><i class="fas fa-arrow-right"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- txt -->
                        <div class="col">
                            <div class="ul-product-details-txt">
                                <!-- product rating -->
                                                                <div class="ul-product-details-rating">
                                                                    <span class="rating">
                                                                        @for ($i = 1; $i <= 5; $i++)
                                                                            @if ($i <= $product->average_rating)
                                                                                <i class="fas fa-star"></i>
                                                                            @else
                                                                                <i class="far fa-star"></i>
                                                                            @endif
                                                                        @endfor
                                                                    </span>
                                                                    <span class="review-number">({{ $product->reviews->where('is_approved', true)->count() }} Reseñas de Clientes)</span>
                                                                </div>
                                
                                                                <!-- price -->
                                                                @if ($product->sale_price)
                                                                    <span class="ul-product-details-price old-price">${{ number_format($product->price, 0, ',', '.') }}</span>
                                                                    <span class="ul-product-details-price">${{ number_format($product->sale_price, 0, ',', '.') }}</span>
                                                                @else
                                                                    <span class="ul-product-details-price">${{ number_format($product->price, 0, ',', '.') }}</span>
                                                                @endif
                                
                                                                <!-- product title -->
                                                                <h3 class="ul-product-details-title">{{ $product->name }}</h3>
                                
                                                                <!-- product description -->
                                                                                                <p class="ul-product-details-descr">{!! $product->short_description !!}</p>
                                                                
                                                                                                                                <!-- product tags -->
                                                                                                                                @if ($product->tags->count() > 0 || $product->occasions->count() > 0 || $product->estilos->count() > 0 || $product->temporadas->count() > 0 || $product->materials->count() > 0)
                                                                                                                                    <div class="ul-product-details-options">

                                                                                                                                        @if ($product->occasions->count() > 0)
                                                                                                                                            <div class="ul-product-details-option ul-product-details-sizes">
                                                                                                                                                <span class="title">Ocasiones:</span>
                                                                                                                                                <div class="variants">
                                                                                                                                                    @foreach ($product->occasions as $occasion)
                                                                                                                                                        <a href="{{ route('shop.index', ['tag' => $occasion->slug]) }}" class="maye-tag-button">{{ $occasion->name }}</a>
                                                                                                                                                    @endforeach
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                        @endif
                                                                                                                                        @if ($product->estilos->count() > 0)
                                                                                                                                            <div class="ul-product-details-option ul-product-details-sizes">
                                                                                                                                                <span class="title">Estilos:</span>
                                                                                                                                                <div class="variants">
                                                                                                                                                    @foreach ($product->estilos as $estilo)
                                                                                                                                                        <a href="{{ route('shop.index', ['tag' => $estilo->slug]) }}" class="maye-tag-button">{{ $estilo->name }}</a>
                                                                                                                                                    @endforeach
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                        @endif
                                                                                                                                        @if ($product->temporadas->count() > 0)
                                                                                                                                            <div class="ul-product-details-option ul-product-details-sizes">
                                                                                                                                                <span class="title">Temporadas:</span>
                                                                                                                                                <div class="variants">
                                                                                                                                                    @foreach ($product->temporadas as $temporada)
                                                                                                                                                        <a href="{{ route('shop.index', ['tag' => $temporada->slug]) }}" class="maye-tag-button">{{ $temporada->name }}</a>
                                                                                                                                                    @endforeach
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                        @endif
                                                                                                                                        @if ($product->materials->count() > 0)
                                                                                                                                            <div class="ul-product-details-option ul-product-details-sizes">
                                                                                                                                                <span class="title">Materiales:</span>
                                                                                                                                                <div class="variants">
                                                                                                                                                    @foreach ($product->materials as $material)
                                                                                                                                                        <a href="{{ route('shop.index', ['tag' => $material->slug]) }}" class="maye-tag-button">{{ $material->name }}</a>
                                                                                                                                                    @endforeach
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                        @endif
                                                                                                                                    </div>
                                                                                                                                @endif                                                                
                                                                                                <!-- product options -->                                                                <div class="ul-product-details-options">
                                                                    <div class="ul-product-details-option ul-product-details-colors">
                                                                        <span class="title">Color</span>
                                                                        <form action="#" class="variants">
                                                                            @foreach ($product->colors as $color)
                                                                                <label for="ul-product-details-color-{{ $color->id }}">
                                                                                    <input type="radio" name="product-color" id="ul-product-details-color-{{ $color->id }}" {{ $loop->first ? 'checked' : '' }} hidden>
                                                                                    <span class="color-btn" style="background-color: {{ $color->hex_code }}"></span>
                                                                                </label>
                                                                            @endforeach
                                                                        </form>
                                                                    </div>
                                                                </div>
                                

                                
                                                                <!-- product actions -->
                                                                <div class="ul-product-details-actions">
                                                                    <div class="left">
                                                                        <button class="add-to-cart" data-product-id="{{ $product->id }}">Añadir al Carrito <span class="icon"><i class="fas fa-shopping-cart"></i></span></button>
                                                                        <input type="hidden" id="product-quantity" value="1">

                                                                    </div>
                                                                    <div class="share-options">
                                                                        <span class="title" style="margin-right: 10px;">Compartir:</span>
                                                                        @php
                                                                            $productUrl = url()->current();
                                                                            $productName = urlencode($product->name);
                                                                        @endphp
                                                                        @foreach ($socialMediaLinks as $link)
                                                                            @php
                                                                                $shareUrl = '#';
                                                                                if ($link->name === 'Facebook') {
                                                                                    $shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . $productUrl;
                                                                                } elseif ($link->name === 'Twitter') {
                                                                                    $shareUrl = 'https://twitter.com/intent/tweet?url=' . $productUrl . '&text=' . $productName;
                                                                                } elseif ($link->name === 'Pinterest') {
                                                                                    $shareUrl = 'http://pinterest.com/pin/create/button/?url=' . $productUrl . '&media=' . asset('storage/' . $product->main_image);
                                                                                } else {
                                                                                    // For others like Instagram, YouTube, TikTok, just link to the profile as they don't have simple share URLs
                                                                                    $shareUrl = $link->url;
                                                                                }
                                                                            @endphp
                                                                            <a href="{{ $shareUrl }}" target="_blank" title="Compartir en {{ $link->name }}"><i class="{{ $link->icon }}"></i></a>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                
                                                <div class="ul-product-details-bottom">
                                                    <!-- description -->
                                                    <div class="ul-product-details-long-descr-wrapper">
                                                        <h3 class="ul-product-details-inner-title">Descripción del Artículo</h3>
                                                        <p>{!! $product->description !!}</p>
                                                    </div>
                                
                                                    <!-- reviews -->
                                                    <div class="ul-product-details-reviews">
                                                        <h3 class="ul-product-details-inner-title">{{ $product->reviews->where('is_approved', true)->count() }} Reseñas</h3>
                                
                                                        @foreach ($product->reviews->where('is_approved', true) as $review)
                                                            <!-- single review -->
                                                            <div class="ul-product-details-review">
                                                                <!-- reviewer image -->
                                                                <div class="ul-product-details-review-reviewer-img">
                                                                    <img src="{{ asset('static/picture/reviewer-img-2.png') }}" alt="Imagen del Reseñador">
                                                                </div>
                                
                                                                <div class="ul-product-details-review-txt">
                                                                    <div class="header">
                                                                        <div class="left">
                                                                            <h4 class="reviewer-name">{{ $review->reviewer_name }}</h4>
                                                                            <h5 class="review-date">{{ $review->created_at->format('M d, Y') }}</h5>
                                                                        </div>
                                
                                                                        <div class="right">
                                                                            <div class="rating">
                                                                                @for ($i = 1; $i <= 5; $i++)
                                                                                    @if ($i <= $review->rating)
                                                                                        <i class="fas fa-star"></i>
                                                                                    @else
                                                                                        <i class="far fa-star"></i>
                                                                                    @endif
                                                                                @endfor
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                
                                                                    <p>{{ $review->review_text }}</p>
                                
                                                                    <button class="ul-product-details-review-reply-btn">Responder</button>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                
                                                                        <!-- review form -->
                                                                        <div class="ul-product-details-review-form-wrapper">
                                                                            <h3 class="ul-product-details-inner-title">Escribir una Reseña</h3>
                                                                            <span class="note">Tu dirección de correo electrónico no será publicada.</span>
                                                    
                                                                            @if (session('success'))
                                                                                <div class="alert alert-success">
                                                                                    {{ session('success') }}
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
                                                    
                                                                            <form class="ul-product-details-review-form" action="{{ route('reviews.store', $product) }}" method="POST">
                                                                                @csrf
                                                                                <div class="form-group rating-field-wrapper">
                                                                                    <span class="title">¿Calificar este producto? *</span>
                                                                                    <div class="rating-field">
                                                                                        <button type="button" data-rating="1"><i class="far fa-star"></i></button>
                                                                                        <button type="button" data-rating="2"><i class="far fa-star"></i></button>
                                                                                        <button type="button" data-rating="3"><i class="far fa-star"></i></button>
                                                                                        <button type="button" data-rating="4"><i class="far fa-star"></i></button>
                                                                                        <button type="button" data-rating="5"><i class="far fa-star"></i></button>
                                                                                        <input type="hidden" name="rating" id="review-rating" value="">
                                                                                    </div>
                                                                                    @error('rating')
                                                                                        <span class="text-danger">{{ $message }}</span>
                                                                                    @enderror
                                                                                </div>
                                                    
                                                                                <div class="row row-cols-2 row-cols-xxs-1 ul-bs-row">
                                                                                    <div class="form-group">
                                                                                        <input type="text" name="reviewer_name" id="review-name" placeholder="Tu Nombre" value="{{ old('reviewer_name') }}">
                                                                                        @error('reviewer_name')
                                                                                            <span class="text-danger">{{ $message }}</span>
                                                                                        @enderror
                                                                                    </div>
                                                    
                                                                                                                    <div class="form-group">
                                                                                                                        <input type="email" name="reviewer_email" id="review-email" placeholder="Tu Correo Electrónico" value="{{ old('reviewer_email') }}">
                                                                                                                        @error('reviewer_email')
                                                                                                                            <span class="text-danger">{{ $message }}</span>
                                                                                                                        @enderror
                                                                                                                    </div>
                                                                                    
                                                                                                                    <div class="form-group">
                                                                                                                        <input type="text" name="reviewer_phone" id="review-phone" placeholder="Tu Número de WhatsApp" value="{{ old('reviewer_phone') }}">
                                                                                                                        @error('reviewer_phone')
                                                                                                                            <span class="text-danger">{{ $message }}</span>
                                                                                                                        @enderror
                                                                                                                    </div>
                                                                                    
                                                                                                                                                    <div class="form-group col-12 mb-4">
                                                                                    
                                                                                                                                                        <textarea name="review_text" id="review-message" placeholder="Tu Reseña">{{ old('review_text') }}</textarea>
                                                                                    
                                                                                                                                                        @error('review_text')
                                                                                    
                                                                                                                                                            <span class="text-danger">{{ $message }}</span>
                                                                                    
                                                                                                                                                        @enderror
                                                                                    
                                                                                                                                                    </div>
                                                                                </div>
                                                    
                                                                                                                                                                                                                            <div class="form-group mb-4">
                                                    
                                                                                                                                                                                                                                {!! NoCaptcha::display() !!}
                                                    
                                                                                                                                                                                                                                @error('g-recaptcha-response')
                                                    
                                                                                                                                                                                                                                    <span class="text-danger">{{ $message }}</span>
                                                    
                                                                                                                                                                                                                                @enderror
                                                    
                                                                                                                                                                                                                            </div>                                                                                                            <div class="form-group">
                                                                                                                <button type="submit">Publicar Reseña <span><i class="fas fa-arrow-up-right-from-square"></i></span></button>
                                                                                                            </div>                                                                            </form>
                                                                        </div>                                                </div>
                                            </div>
                                        </div>
                                        <!-- MAIN CONTENT SECTION END -->
                                    </main>

    @push('scripts')
        <script>
            function showCartConfirmationModal(title, message, type) {
                const modalElement = document.getElementById('cartConfirmationModal');
                const modalTitle = document.getElementById('cartConfirmationModalLabel');
                const modalBody = document.getElementById('cartConfirmationModalBody');

                modalTitle.textContent = title;
                modalBody.innerHTML = `<p>${message}</p>`; // Use innerHTML for potential HTML in message

                const modal = new bootstrap.Modal(modalElement);
                modal.show();

                modalElement.addEventListener('hidden.bs.modal', function () {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());
                    document.body.style.overflow = ''; // Restore body scroll
                }, { once: true }); // Use 'once' to auto-remove the listener
            }

            document.addEventListener('DOMContentLoaded', function () {
                // Temporarily commented out rating buttons logic
                /*
                const ratingButtons = document.querySelectorAll('.rating-field button');
                const reviewRatingInput = document.getElementById('review-rating');
                
                ratingButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        const rating = this.dataset.rating;
                        reviewRatingInput.value = rating;
                        
                        ratingButtons.forEach(btn => {
                            const icon = btn.querySelector('i');
                            const btnRating = btn.dataset.rating;
                            if (btnRating <= rating) {
                                icon.classList.remove('far', 'fa-star');
                                icon.classList.add('fas', 'fa-star');
                            } else {
                                icon.classList.remove('fas', 'fa-star');
                                icon.classList.add('far', 'fa-star');
                            }
                        });
                    });
                });
                */
                
                // Add to Cart functionality
                const addToCartBtn = document.querySelector('.add-to-cart');
                console.log('addToCartBtn element:', addToCartBtn); // Debug log for element existence
                if (addToCartBtn) {
                    addToCartBtn.addEventListener('click', function() {
                        console.log('Add to Cart button clicked'); // Debug log
                        const productId = this.dataset.productId;
                        const quantity = document.getElementById('product-quantity').value;
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        
                        // Disable the button to prevent multiple clicks
                        addToCartBtn.disabled = true;
                        addToCartBtn.innerHTML = 'Añadiendo... <span class="icon"><i class="fas fa-shopping-cart"></i></span>';

                        fetch('{{ route('cart.add') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                product_id: productId,
                                quantity: quantity
                            })
                        })
                        .then(response => {
                            console.log('Fetch Response Object:', response); // Debug log: inspect the raw response object
                            if (response.status === 409) {
                                return response.json().then(data => {
                                    console.log('409 Response Data (direct):', data); // Debug log: inspect the parsed JSON data directly
                                    showCartConfirmationModal('Producto ya en el carrito', data.message, 'warning');
                                    addToCartBtn.innerHTML = 'En el Carrito <span class="icon"><i class="fas fa-check"></i></span>';
                                    return Promise.reject('Product already in cart');
                                });
                            }
                            if (!response.ok) { // Handle other non-2xx responses
                                return response.json().then(data => {
                                    const message = data && data.message ? data.message : 'Hubo un error al añadir el producto al carrito.'; // Safety check
                                    showCartConfirmationModal('Error', message, 'danger');
                                    addToCartBtn.disabled = false; // Re-enable on generic error
                                    addToCartBtn.innerHTML = 'Añadir al Carrito <span class="icon"><i class="fas fa-shopping-cart"></i></span>';
                                    return Promise.reject(message);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.message) {
                                showCartConfirmationModal('Producto Añadido', data.message, 'success');
                            }
                            if (data.cartCount !== undefined) {
                                const cartCountElement = document.getElementById('cart-count');
                                if (cartCountElement) {
                                    cartCountElement.textContent = data.cartCount;
                                    if (data.cartCount > 0) {
                                        cartCountElement.classList.remove('d-none');
                                    } else {
                                        cartCountElement.classList.add('d-none');
                                    }
                                }
                            }
                            addToCartBtn.innerHTML = 'En el Carrito <span class="icon"><i class="fas fa-check"></i></span>';
                        })
                        .catch(error => {
                            console.error('Error adding to cart:', error);
                            if (error === 'Product already in cart') {
                                // The modal was already shown in the 409 block, so no need to show it again here.
                                // We just prevent the generic error modal from appearing.
                            } else {
                                showCartConfirmationModal('Error', 'Fallo al añadir el producto al carrito.', 'danger');
                                addToCartBtn.disabled = false; // Re-enable on generic error
                                addToCartBtn.innerHTML = 'Añadir al Carrito <span class="icon"><i class="fas fa-shopping-cart"></i></span>';
                            }
                        });
                    });
                }
            });        </script>    @endpush
@endsection
