@extends('layouts.app')

@section('title', 'Términos y Condiciones - Maye Shop')

@section('meta_description', 'Lee nuestros términos y condiciones de servicio. Al usar nuestro sitio web y realizar compras, aceptas nuestras políticas.')
@section('meta_keywords', 'términos y condiciones, políticas, legal, Maye Shop')

@section('meta_tags')
    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="@yield('meta_description')">
    <meta property="og:url" content="{{ route('terms') }}">
    <meta property="og:type" content="website">
@endsection

@section('content')
    <x-breadcrumb :title="'Términos y Condiciones'" :crumbs="$crumbs" />

    <div class="ul-page-content ul-terms-conditions-page pt-120 pb-120">
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
