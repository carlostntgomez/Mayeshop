<div class="ul-container">
    <section class="ul-blogs">
        <div class="ul-inner-container">
            <!-- heading -->
            <div class="ul-section-heading">
                <div class="left">
                    <span class="ul-section-sub-title">Noticias y Blog</span>
                    <h2 class="ul-section-title">Últimas Noticias y Blog</h2>
                </div>

                <div>
                    <a href="blog.html" class="ul-blogs-heading-btn">Ver Todo el Blog <i class="fas fa-arrow-up-right-from-square"></i></a>
                </div>
            </div>

            <!-- blog grid -->
            <div class="row ul-bs-row row-cols-md-3 row-cols-2 row-cols-xxs-1">
            @foreach ($posts as $post)
                <!-- single blog -->
                <div class="col">
                    <div class="ul-blog">
                        <div class="ul-blog-img">
                            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="Article Image">

                            <div class="date">
                                <span class="number">{{ $post->created_at->format('d') }}</span>
                                <span class="txt">{{ $post->created_at->format('M') }}</span>
                            </div>
                        </div>

                        <div class="ul-blog-txt">
                            <div class="ul-blog-infos flex gap-x-[30px] mb-[16px]">
                                <!-- single info -->
                                <div class="ul-blog-info">
                                    <span class="icon"><i class="fas fa-user"></i></span>
                                    <span class="text font-normal text-[14px] text-etGray">Por Admin</span>
                                </div>
                            </div>

                            <h3 class="ul-blog-title"><a href="{{ route('blog.show', ['blogCategory' => $post->blogCategory->slug, 'post' => $post->slug]) }}">{{ $post->title }}</a></h3>
                            <p class="ul-blog-descr">{{ $post->description }}</p>

                            <a href="{{ route('blog.show', ['blogCategory' => $post->blogCategory->slug, 'post' => $post->slug]) }}" class="ul-blog-btn">Leer Más <span class="icon"><i class="fas fa-arrow-up-right-from-square"></i></span></a>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        </div>
    </section>
</div>