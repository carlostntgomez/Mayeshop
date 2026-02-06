<div class="ul-container">
    <section class="ul-ad">
        <div class="ul-inner-container">
            <div class="ul-ad-content">
                <div class="ul-ad-txt">
                    <span class="ul-ad-sub-title">Productos en Tendencia</span>
                    @if($maxDiscount > 0)
                        <h2 class="ul-section-title">¡Obtén hasta un {{ $maxDiscount }}% de Descuento!</h2>
                    @else
                        <h2 class="ul-section-title">¡Explora Nuestras Colecciones!</h2>
                    @endif
                    @if($topCategories->isNotEmpty())
                        <div class="ul-ad-categories">
                            @foreach($topCategories as $category)
                                <span class="category"><span><i class="fas fa-check"></i></span>{{ $category->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="ul-ad-img">
                    <img src="{{ asset('static/picture/ad-img.png') }}" alt="Ad Image">
                </div>

                <a href="{{ route('shop.index', ['on_sale' => true]) }}" class="ul-btn">Ver Descuentos <i class="fas fa-arrow-up-right-from-square"></i></a>
            </div>
        </div>
    </section>
</div>