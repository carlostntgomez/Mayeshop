@extends('layouts.app')

@section('title', 'Política de Privacidad - Maye Shop')

@section('meta_description', 'Conoce cómo manejamos y protegemos tus datos personales en Maye Shop. Nuestra política de privacidad explica qué información recopilamos y cómo la usamos.')
@section('meta_keywords', 'política de privacidad, privacidad, datos personales, protección de datos, Maye Shop')

@section('meta_tags')
    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="@yield('meta_description')">
    <meta property="og:url" content="{{ route('privacy') }}">
    <meta property="og:type" content="website">
@endsection

@section('content')
    <x-breadcrumb :title="'Política de Privacidad'" :crumbs="$crumbs" />

    <div class="ul-page-content ul-privacy-policy-page pt-120 pb-120">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="legal-page-content-wrapper">
                        <div class="ul-content-card">
                            {!! $legalPage->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
