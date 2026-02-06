@extends('layouts.app')

@section('title', $post->meta_title ?? $post->title)

@section('meta_description', $post->meta_description)
@section('meta_keywords', $post->meta_keywords)

@section('meta_tags')
    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="@yield('meta_description')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="article">
    @if($post->featured_image)
        <meta property="og:image" content="{{ asset('storage/' . $post->featured_image) }}">
    @endif
@endsection

@section('content')
    <x-breadcrumb :title="'Detalles del Blog'" :crumbs="$crumbs" />


    <!-- BLOG SECTION START -->
    <section>
        <div class="ul-inner-page-container">
            <div class="row ul-bs-row">
                <div class="col-xxxl-9 col-lg-8 col-md-7">
                    <div>
                        <div class="ul-blog-details">
                            <div class="ul-blog ul-blog-big">
                                <h3 class="ul-blog-title"><a href="">{{ $post->title }}</a></h3>

                                <div class="ul-blog-img">
                                    <img src="{{ asset('storage/' . $post->featured_image) }}" alt="Blog Image">

                                    <div class="ul-blog-infos ul-blog-details-infos flex gap-x-[30px] mb-[16px]">
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
                                </div>

                                <div class="ul-blog-txt">
                                    <p class="ul-blog-descr">{!! $post->content !!}</p>

                                <!-- actions -->
                                <div class="ul-blog-details-actions">
                                    <!-- tags -->
                                    <div class="tags-wrapper">
                                        <div class="ul-blog-tags tags">
                                            @foreach ($post->blogTags as $tag)
                                                <a href="{{ route('blog.tag.show', $tag->slug) }}">{{ $tag->name }}</a>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- share -->
                                    <div class="share">
                                        <div class="share-options">
                                            <a href="#"><i class="fab fa-facebook"></i></a>
                                            <a href="#"><i class="fab fa-twitter"></i></a>
                                            <a href="#"><i class="fab fa-linkedin"></i></a>
                                            <a href="#"><i class="fab fa-youtube"></i></a>
                                        </div>
                                    </div>
                                </div>

                                <!-- nav -->
                                <div class="ul-blog-details-nav">
                                    <div class="nav-item prev">
                                        <a href="" class="icon-link"><i class="fas fa-arrow-left"></i></a>
                                        <a href="" class="text-link">Post Anterior</a>
                                    </div>

                                    <div class="nav-item prev">
                                        <a href="" class="text-link">Post Siguiente</a>
                                        <a href="" class="icon-link"><i class="fas fa-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                    </div>
                </div>

                <x-blog-sidebar :recentPosts="$recentPosts" :blogCategories="$blogCategories" :blogTags="$blogTags" />
            </div>
        </div>
    </section>
    <!-- BLOG SECTION END -->
@endsection
