<div class="col-xxxl-3 col-lg-4 col-md-5">
    <div class="ul-blog-sidebar">
        <!-- single widget /search -->
        <div class="ul-blog-sidebar-widget ul-blog-sidebar-search">
            <div class="ul-blog-sidebar-widget-content">
                <form action="#" class="ul-blog-search-form">
                    <input type="search" name="blog-search" id="ul-blog-search" placeholder="Buscar aquí">
                    <button type="submit"><span class="icon"><i class="fas fa-search"></i></span></button>
                </form>
            </div>
        </div>

        <!-- single widget / Recent Posts -->
        <div class="ul-blog-sidebar-widget ul-blog-sidebar-recent-post">
            <h3 class="ul-blog-sidebar-widget-title">Posts Recientes</h3>
            <div class="ul-blog-sidebar-widget-content">
                <div class="ul-blog-recent-posts">
                    @foreach ($recentPosts as $recentPost)
                        <!-- single post -->
                        <div class="ul-blog-recent-post">
                            <div class="img">
                            <img src="{{ asset('storage/' . $recentPost->featured_image) }}" alt="Post Image">
                            </div>

                            <div class="txt">
                                <span class="date">
                                    <span class="icon"><i class="fas fa-calendar-alt"></i></span>
                                    <span>{{ $recentPost->created_at->format('M d, Y') }}</span>
                                </span>

                                <h4 class="title"><a href="{{ route('blog.show', ['blogCategory' => $recentPost->blogCategory->slug, 'post' => $recentPost->slug]) }}">{{ $recentPost->title }}</a></h4>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- single widget / Categories -->
        <div class="ul-blog-sidebar-widget ul-blog-sidebar-recent-post">
            <h3 class="ul-blog-sidebar-widget-title">Categorías</h3>
            <div class="ul-blog-sidebar-widget-content">
                <div class="ul-blog-tags">
                    @foreach ($blogCategories as $blogCategory)
                        <a href="{{ route('blog.category.show', $blogCategory->slug) }}">{{ $blogCategory->name }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- single widget / Tags -->
        <div class="ul-blog-sidebar-widget ul-blog-sidebar-recent-post">
            <h3 class="ul-blog-sidebar-widget-title">Etiquetas</h3>
            <div class="ul-blog-sidebar-widget-content">
                <div class="ul-blog-tags">
                    @foreach ($blogTags as $blogTag)
                        <a href="{{ route('blog.tag.show', $blogTag->slug) }}">{{ $blogTag->name }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="ul-blog-sidebar-widget ad-banner">
            <a href="shop.html"><img src="{{ asset('static/picture/gallery-item-4.jpg') }}" alt="ad banner"></a>
        </div>
    </div>
</div>