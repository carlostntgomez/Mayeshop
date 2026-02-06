<section class="ul-reviews overflow-hidden">
    <div class="ul-section-heading text-center justify-content-center">
        <div>
            <span class="ul-section-sub-title">Reseñas de Clientes</span>
            <h2 class="ul-section-title">Reseñas de Productos</h2>
            <p class="ul-reviews-heading-descr">Nuestras referencias son muy valiosas, el resultado de un gran esfuerzo...</p>
        </div>
    </div>

    <!-- slider -->
    <div class="ul-reviews-slider swiper">
        <div class="swiper-wrapper">
            @foreach ($reviews as $review)
                <!-- single review -->
                <div class="swiper-slide">
                    <div class="ul-review">
                        <div class="ul-review-rating">
                            @for ($i = 0; $i < $review->rating; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                            @for ($i = 0; $i < (5 - $review->rating); $i++)
                                <i class="far fa-star"></i>
                            @endfor
                        </div>
                        <p class="ul-review-descr">{{ $review->review_text }}</p>
                        <div class="ul-review-bottom">
                            <div class="ul-review-reviewer">
                                <div class="reviewer-image"><img src="{{ asset('static/picture/review-author-1.png') }}" alt="reviewer image"></div>
                                <div>
                                    <h3 class="reviewer-name">{{ $review->reviewer_name }}</h3>
                                    <span class="reviewer-role">{{ $review->product->name }}</span>
                                </div>
                            </div>

                            <!-- icon -->
                            <div class="ul-review-icon"><i class="fas fa-quote-left"></i></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>