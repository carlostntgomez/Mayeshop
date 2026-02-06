@extends('layouts.app')

@extends('layouts.app')

@section('title', 'Contacto - Maye Shop')
@section('meta_description', 'Ponte en contacto con Maye Shop. Estamos aquí para responder tus preguntas, ayudarte con tus compras o escuchar tus comentarios. Contáctanos hoy.')
@section('meta_keywords', 'contacto, contactar, soporte, ayuda, Maye Shop, atención al cliente')

@section('meta_tags')
    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="@yield('meta_description')">
    <meta property="og:url" content="{{ route('contact') }}">
    <meta property="og:type" content="website">
@endsection

@section('content')
    @if($contactContent)
        <x-breadcrumb :title="$contactContent->breadcrumb_title" :crumbs="$crumbs" />
    @endif

        <main>
            <!-- CONTACT INFO SECTION START -->
            <section class="ul-contact-infos">
                <!-- single contact info -->
                <div class="ul-contact-info">
                    <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="txt">
                        <h6 class="title">Nuestra Dirección</h6>
                        <p class="descr mb-0">{{ $contactContent->address }}</p>
                    </div>
                </div>

                <!-- single contact info -->
                <div class="ul-contact-info">
                    <div class="icon"><i class="fas fa-user"></i></div>
                    <div class="txt">
                        <h6 class="title">Número de Teléfono</h6>
                        <p class="descr mb-0">
                            <a href="tel:{{ $contactContent->phone }}">{{ $contactContent->phone }}</a>
                        </p>
                    </div>
                </div>

                <!-- single contact info -->
                <div class="ul-contact-info">
                    <div class="icon"><i class="fas fa-envelope"></i></div>
                    <div class="txt">
                        <h6 class="title">Correo Electrónico</h6>
                        <p class="descr mb-0">
                            <a href="mailto:{{ $contactContent->email }}">{{ $contactContent->email }}</a>
                        </p>
                    </div>
                </div>
            </section>
            <!-- CONTACT INFO SECTION END -->

            <!-- MAP AREA START -->
            @if($contactContent->map_embed_code)
                <div class="ul-contact-map">
                    {!! $contactContent->map_embed_code !!}
                </div>
            @endif
            <!-- MAP AREA END -->


        </main>
    @endif
@endsection
