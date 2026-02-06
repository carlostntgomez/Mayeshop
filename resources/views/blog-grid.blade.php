@extends('layouts.app')

@section('title', 'Blog de Moda - Maye Shop | Tendencias, Lujo y Estilo')

@section('meta_description', 'Lee nuestro blog de moda para descubrir las últimas tendencias, consejos de estilo, guías de lujo y más. Mantente al día con el mundo de la moda en Maye Shop.')
@section('meta_keywords', 'blog de moda, tendencias, estilo, lujo, Maye Shop, consejos de moda, vestidos de lujo')

@section('meta_tags')
    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="@yield('meta_description')">
    <meta property="og:url" content="{{ route('blog.index') }}">
    <meta property="og:type" content="website">
@endsection

@section('content')
    <x-breadcrumb :title="'Blog'" :crumbs="$crumbs" />


    <!-- BLOG SECTION START -->
    <section>
        <div class="ul-inner-page-container">
            <div class="row ul-bs-row">
                <div class="col-xxxl-9 col-lg-8 col-md-7">
                                        <!-- blogs -->
                                        <div>
                                            @foreach ($posts as $post)
                                                <!-- single blog -->
                                                <div class="ul-blog ul-blog-big">
                                                    <div class="ul-blog-img">
                                                        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="Blog Image">
                    
                                                        <div class="date">
                                                            <span class="number">{{ $post->published_at->format('d') }}</span>
                                                            <span class="txt">{{ $post->published_at->format('M') }}</span>
                                                        </div>
                                                    </div>
                    
                                                    <div class="ul-blog-txt">
                                                        <div class="ul-blog-infos flex gap-x-[30px] mb-[16px]">
                                                            <!-- single info -->
                                                            <div class="ul-blog-info">
                                                                <span class="icon"><i class="fas fa-user"></i></span>
                                                                <span class="text font-normal text-[14px] text-etGray">Por Admin</span>
                                                            </div>
                                                            <!-- single info -->
                                                            <div class="ul-blog-info">
                                                                <span class="icon"><i class="fas fa-calendar-alt"></i></span>
                                                                <span class="text font-normal text-[14px] text-etGray">{{ $post->published_at->format('M d, Y') }}</span>
                                                            </div>
                                                        </div>
                    
                                                        <h3 class="ul-blog-title"><a href="{{ route('blog.show', ['blogCategory' => $post->blogCategory->slug, 'post' => $post->slug]) }}">{{ $post->title }}</a></h3>
                                                        <p class="ul-blog-descr">{{ $post->description }}</p>
                    
                                                        <a href="{{ route('blog.show', ['blogCategory' => $post->blogCategory->slug, 'post' => $post->slug]) }}" class="ul-blog-btn ul-blog-big-btn">Leer Más <span class="icon"><i class="fas fa-arrow-up-right-from-square"></i></span></a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                    <!-- pagination -->
                    <div class="ul-pagination pt-0 border-0">
                        {{ $posts->links() }}
                    </div>
                </div>

                <x-blog-sidebar :recentPosts="$recentPosts" :blogCategories="$blogCategories" :blogTags="$blogTags" />
            </div>
        </div>
    </section>
    <!-- BLOG SECTION END -->
@endsection