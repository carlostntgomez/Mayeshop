@extends('layouts.app')

@section('title', 'Política de Reembolso - Maye Shop')

@section('meta_description', 'Consulta nuestra política de reembolso para obtener información sobre devoluciones, cambios y cómo procesamos los reembolsos en Maye Shop.')
@section('meta_keywords', 'política de reembolso, reembolso, devolución, cambio, Maye Shop')

@section('meta_tags')
    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="@yield('meta_description')">
    <meta property="og:url" content="{{ route('refund') }}">
    <meta property="og:type" content="website">
@endsection

@section('content')
    <x-breadcrumb :title="'Política de Reembolso'" :crumbs="$crumbs" />

    <div class="ul-page-content ul-refund-policy-page pt-120 pb-120">
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
