@extends('layouts.app')

@section('title', $tag->meta_title ?? $tag->name)
@section('meta_description', $tag->meta_description)
@section('meta_keywords', $tag->meta_keywords)

@section('meta_tags')
    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="@yield('meta_description')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
@endsection

@section('content')
    <x-breadcrumb :title="$tag->name" :crumbs="$crumbs" />

    <!-- MAIN CONTENT SECTION START -->
    <section>
        <div class="ul-inner-page-container">
            <div class="row ul-bs-row">
                <div class="col-xxxl-9 col-lg-8 col-md-7">
                    <div class="row ul-bs-row row-cols-lg-3 row-cols-2 row-cols-xxs-1">
                        @foreach ($posts as $post)
                            <!-- post card -->
                            <div class="col">
                                <div class="ul-blog">
                                    <div class="ul-blog-img">
                                        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}">
                                        <div class="date">
                                            <span class="number">{{ $post->created_at->format('d') }}</span>
                                            <span class="txt">{{ $post->created_at->format('M') }}</span>
                                        </div>
                                    </div>
                                    <div class="ul-blog-txt">
                                        <h3 class="ul-blog-title"><a href="{{ route('blog.show', ['blogCategory' => $post->blogCategory->slug, 'post' => $post->slug]) }}">{{ $post->title }}</a></h3>
                                        <p class="ul-blog-descr">{{ $post->excerpt }}</p>
                                        <a href="{{ route('blog.show', ['blogCategory' => $post->blogCategory->slug, 'post' => $post->slug]) }}" class="ul-blog-btn">Leer Más <span class="icon"><i class="fas fa-arrow-up-right-from-square"></i></span></a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- pagination -->
                    <div class="ul-pagination">
                        {{ $posts->links() }}
                    </div>
                </div>

                <x-blog-sidebar :recentPosts="$recentPosts" :blogCategories="$blogCategories" :blogTags="$blogTags" />
            </div>
        </div>
    </section>
    <!-- MAIN CONTENT SECTION END -->
@endsection