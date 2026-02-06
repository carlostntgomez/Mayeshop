<div class="ul-gallery overflow-hidden mx-auto">
    <div class="ul-gallery-slider swiper">
        <div class="swiper-wrapper">
            @foreach ($galleryImages as $galleryImage)
                <!-- single gallery item -->
                <div class="ul-gallery-item swiper-slide">
                    <img src="{{ asset('storage/' . $galleryImage->image_path) }}" alt="Gallery Image">
                    <div class="ul-gallery-item-btn-wrapper">
                        <a href="{{ asset('storage/' . $galleryImage->image_path) }}" data-fslightbox="gallery"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>