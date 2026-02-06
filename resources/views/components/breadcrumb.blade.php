@props(['title', 'crumbs' => []])

<!-- BREADCRUMB SECTION START -->
<div class="ul-container">
    <div class="ul-breadcrumb">
        <h2 class="ul-breadcrumb-title">{{ $title }}</h2>
        <div class="ul-breadcrumb-nav">
            @foreach ($crumbs as $crumb)
                @if (!$loop->first)
                    <i class="fas fa-chevron-left"></i>
                @endif

                @if ($loop->last || !isset($crumb['url']))
                    <span class="current-page">{{ $crumb['name'] }}</span>
                @else
                    <a href="{{ $crumb['url'] }}">
                        @if ($loop->first)
                            <i class="fas fa-home"></i>
                        @endif
                        {{ $crumb['name'] }}
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</div>
<!-- BREADCRUMB SECTION END -->
