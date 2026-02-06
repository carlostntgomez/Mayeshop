@extends('layouts.app')

@extends('layouts.app')

@section('title', 'Preguntas Frecuentes - Maye Shop')
@section('meta_description', 'Encuentra respuestas a las preguntas más frecuentes sobre compras, envíos, devoluciones y más en Maye Shop. Estamos aquí para ayudarte.')
@section('meta_keywords', 'preguntas frecuentes, FAQ, ayuda, soporte, Maye Shop, envíos, devoluciones')

@section('meta_tags')
    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="@yield('meta_description')">
    <meta property="og:url" content="{{ route('faq') }}">
    <meta property="og:type" content="website">
@endsection

@section('content')
    <x-breadcrumb :title="'Preguntas Frecuentes'" :crumbs="$crumbs" />

    <main>
        <!-- FAQ SECTION START -->
        <section class="ul-faq">
            <div class="ul-inner-page-container">
                <div class="ul-faq-heading">
                    <h2 class="ul-section-title">Preguntas Frecuentes</h2>
                    <p class="ul-faq-heading-descr">Aquí encontrarás respuestas a las preguntas más comunes sobre nuestros productos y servicios.</p>
                </div>

                <div class="ul-accordion" id="faqAccordion">
                    @forelse($faqs as $faq)
                        <!-- single question -->
                        <div class="ul-single-accordion-item">
                            <div class="ul-single-accordion-item__header">
                                <div class="left">
                                    <h3 class="ul-single-accordion-item__title">{{ $faq->question }}</h3>
                                </div>
                                <span class="icon"><i class="fas fa-plus"></i></span>
                            </div>

                            <div class="ul-single-accordion-item__body">
                                <p class="mb-0">{{ $faq->answer }}</p>
                            </div>
                        </div>
                    @empty
                        <p>No hay preguntas frecuentes disponibles en este momento.</p>
                    @endforelse
                </div>
            </div>
        </section>
        <!-- FAQ SECTION END -->
    </main>
@endsection
