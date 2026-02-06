<div class="mb-12">
    <!-- BANNER SECTION START -->
    <div class="overflow-hidden">
        <div class="ul-container">
            <section class="ul-banner">
                <div class="ul-banner-slider-wrapper">
                    <div class="ul-banner-slider swiper">
                        <div class="swiper-wrapper">
                            @foreach($banners as $banner)
                                <!-- single slide -->
                                <div class="swiper-slide ul-banner-slide">
                                    <div class="ul-banner-slide-img">
                                        <img src="{{ asset('storage/' . $banner->image_path) }}" alt="{{ $banner->title }}">
                                    </div>
                                    <div class="ul-banner-slide-txt">
                                        <span class="ul-banner-slide-sub-title">{{ $banner->subtitle }}</span>
                                        <h1 class="ul-banner-slide-title">{{ $banner->title }}</h1>
                                        <p class="ul-banner-slide-price">{{ $banner->price_text }}</p>
                                        <a href="{{ $banner->button_url }}" class="ul-btn">{{ $banner->button_text }} <i class="fas fa-arrow-up-right-from-square"></i></a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- slider navigation -->
                        <div class="ul-banner-slider-nav-wrapper">
                            <div class="ul-banner-slider-nav">
                                <button class="prev"><span class="icon"><i class="fas fa-chevron-down"></i></span></button>
                                <button class="next"><span class="icon"><i class="fas fa-chevron-down"></i></span></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ul-banner-img-slider-wrapper">
                    <div class="ul-banner-img-slider swiper overflow-visible">
                        <div class="swiper-wrapper">
                            @foreach($banners as $banner)
                                <div class="swiper-slide">
                                    <img src="{{ asset('storage/' . $banner->image_path) }}" alt="{{ $banner->title }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <!-- BANNER SECTION END -->
</div>