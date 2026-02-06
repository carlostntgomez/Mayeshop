<div class="ul-container">
    <section class="ul-categories">
        <div class="ul-inner-container">
            <div class="row row-cols-lg-3 row-cols-md-3 row-cols-2 row-cols-xxs-1 ul-bs-row">
                @foreach($categories as $category)
                    <!-- single category -->
                    <div class="col">
                        <a class="ul-category" href="{{ route('category.show', ['productType' => $category->productType, 'category' => $category]) }}">
                            <div class="ul-category-img">
                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
                            </div>
                            <div class="ul-category-txt">
                                <span>{{ $category->name }}</span>
                            </div>
                            <div class="ul-category-btn">
                                <span><i class="fas fa-arrow-right"></i></span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>