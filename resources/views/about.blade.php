@extends('layouts.app')

@extends('layouts.app')

@section('title', 'Acerca de Nosotros - Maye Shop')
@section('meta_description', 'Conoce la historia y la misión de Maye Shop. Descubre nuestra pasión por la moda, el lujo y el estilo, y por qué somos tu mejor opción para encontrar prendas únicas.')
@section('meta_keywords', 'acerca de, nosotros, historia, misión, Maye Shop, moda de lujo')

@section('meta_tags')
    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="@yield('meta_description')">
    <meta property="og:url" content="{{ route('about') }}">
    <meta property="og:type" content="website">
@endsection

@section('content')
    @if($aboutContent)
        <x-breadcrumb :title="$aboutContent->breadcrumb_title" :crumbs="$crumbs" />
    @endif

    <main>
        @if($aboutContent)
            <!-- ABOUT COVER AREA START -->
            <div class="ul-container">
                <div class="ul-about-cover-img">
                    <img src="{{ asset('storage/' . $aboutContent->cover_image) }}" alt="Imagen de Portada">
                </div>
            </div>
            <!-- ABOUT COVER AREA END -->


            <!-- ABOUT SECTION START -->
            <div class="ul-inner-page-container my-0">
                <section class="ul-about">
                    <div class="row row-cols-md-2 row-cols-1 align-items-center ul-bs-row">
                        <!-- txt -->
                        <div class="col">
                            <div class="ul-about-txt">
                                <span class="ul-section-sub-title">{{ $aboutContent->section1_subtitle }}</span>
                                <h2 class="ul-section-title">{{ $aboutContent->section1_title }}</h2>
                                <p>{{ $aboutContent->section1_paragraph }}</p>
                            </div>
                        </div>

                        <!-- img -->
                        <div class="col">
                            <div class="ul-about-img">
                                <img src="{{ asset('storage/' . $aboutContent->section1_image) }}" alt="Imagen Sobre Nosotros">
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <!-- ABOUT SECTION END -->


            <!-- ABOUT SECTION START -->
            <div class="ul-inner-page-container my-0">
                <section class="ul-about">
                    <div class="row row-cols-md-2 row-cols-1 align-items-center ul-bs-row">
                        <!-- img -->
                        <div class="col">
                            <div class="ul-about-img">
                                <img src="{{ asset('storage/' . $aboutContent->section2_image) }}" alt="Imagen Sobre Nosotros">
                            </div>
                        </div>

                        <!-- txt -->
                        <div class="col">
                            <div class="ul-about-txt">
                                <span class="ul-section-sub-title">{{ $aboutContent->section2_subtitle }}</span>
                                <h2 class="ul-section-title">{{ $aboutContent->section2_title }}</h2>
                                <p>{{ $aboutContent->section2_paragraph }}</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <!-- ABOUT SECTION END -->


            <!-- MORE ABOUT US SECTION START -->
            <div class="ul-inner-page-container mb-0">
                <div class="ul-more-about">
                    <!-- heading -->
                    <div class="ul-more-about-heading">
                        <h2 class="ul-section-title">{{ $aboutContent->more_about_heading_title }}</h2>
                        <p class="ul-more-about-heading-descr">{{ $aboutContent->more_about_heading_description }}</p>
                    </div>

                    <!-- row -->
                    <div class="row row-cols-lg-3 row-cols-sm-2 row-cols-1 ul-more-about-row">
                        <!-- single point -->
                        <div class="col">
                            <div class="ul-more-about-point">
                                <h3 class="ul-more-about-point-title">{{ $aboutContent->point1_title }}</h3>
                                <p class="ul-more-about-point-descr">{{ $aboutContent->point1_description }}</p>
                            </div>
                        </div>

                        <!-- single point -->
                        <div class="col">
                            <div class="ul-more-about-point">
                                <h3 class="ul-more-about-point-title">{{ $aboutContent->point2_title }}</h3>
                                <p class="ul-more-about-point-descr">{{ $aboutContent->point2_description }}</p>
                            </div>
                        </div>

                        <!-- single point -->
                        <div class="col">
                            <div class="ul-more-about-point">
                                <h3 class="ul-more-about-point-title">{{ $aboutContent->point3_title }}</h3>
                                <p class="ul-more-about-point-descr">{{ $aboutContent->point3_description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- MORE ABOUT US SECTION END -->
        @endif

        <x-reviews-section :reviews="$reviews" />
    </main>
@endsection
