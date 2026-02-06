<div class="col-lg-3 col-md-4">
    <div class="ul-products-sidebar">
        <!-- single widget / search -->
        <div class="ul-products-sidebar-widget ul-products-search">
            <form action="{{ url()->current() }}" method="GET" class="ul-products-search-form">
                <input type="text" name="search" id="ul-products-search-field" placeholder="Buscar artículos" value="{{ request('search') }}">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>



                                <!-- single widget / categories -->
                                <div class="ul-products-sidebar-widget ul-products-categories">
<h3>Categorías</h3>
        
                                    <div class="ul-products-categories-link">
                                        @foreach ($categories as $category)
                                            <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->query(), ['category' => $category->slug])) }}"><span><i class="fas fa-arrow-right"></i> {{ $category->name }}</span></a>
                                        @endforeach
                                    </div>
                                </div>
                                <!-- single widget / color filter -->
                                <div class="ul-products-sidebar-widget ul-products-color-filter">
                                    <h3>Filtrar por color</h3>
        
                                    <div class="ul-products-color-filter-colors">
                                        @foreach ($colors as $color)
                                            <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->query(), ['color' => $color->name])) }}" class="{{ strtolower($color->name) }}">
                                                <span class="left"><span class="color-prview" style="background-color: {{ $color->hex_code }}"></span> {{ $color->name }}</span>
                                                <span>{{ $color->products->count() }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>


        
                                                        <!-- single widget / tags -->
                        <div class="ul-products-sidebar-widget">
                            <h3>Etiquetas</h3>

                            <div class="ul-products-categories-link">
                                @foreach ($tags->groupBy('type') as $type => $tags)
                                    <h4 class="ul-products-sidebar-widget-subtitle">{{ App\Models\Tag::TYPES[$type] }}</h4>
                                    @foreach ($tags as $tag)
                                        <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->query(), ['tag' => $tag->slug])) }}"><span><i class="fas fa-arrow-right"></i> {{ $tag->name }}</span></a>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>

    </div>
</div>