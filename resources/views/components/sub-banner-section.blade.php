<div class="ul-container">
    <section class="ul-sub-banners">
        <div class="ul-inner-container">
            <div class="row ul-bs-row row-cols-md-3 row-cols-sm-2 row-cols-1">
                @foreach ($homepageSubBanners as $subBanner)
                    <!-- single sub banner -->
                    <div class="col">
                        <div class="ul-sub-banner " @if($subBanner->background_color) style="background-color: {{ $subBanner->background_color }};" @endif>
                            <div class="ul-sub-banner-txt">
                                <div class="top">
                                    <span class="ul-ad-sub-title">{{ $subBanner->subtitle }}</span>
                                    <h3 class="ul-sub-banner-title">{{ $subBanner->title }}</h3>
                                    <p class="ul-sub-banner-descr">{{ $subBanner->description }}</p>
                                </div>

                                <div class="bottom">
                                    <a href="{{ $subBanner->link }}" class="ul-sub-banner-btn">Colección <i class="fas fa-arrow-up-right-from-square"></i></a>
                                </div>
                            </div>

                            <div class="ul-sub-banner-img">
                                <img src="{{ asset('storage/' . $subBanner->image_path) }}" alt="Sub Banner Image">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>