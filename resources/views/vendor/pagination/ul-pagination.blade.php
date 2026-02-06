@if ($paginator->hasPages())
    <div class="ul-pagination">
        <ul>
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li><a href="#"><i class="fas fa-arrow-left"></i></a></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}"><i class="fas fa-arrow-left"></i></a></li>
            @endif

            {{-- Pagination Elements --}}
            <li class="pages">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <a href="#">{{ $element }}</a>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <a href="#" class="active">{{ sprintf("%02d", $page) }}</a>
                            @else
                                <a href="{{ $url }}">{{ sprintf("%02d", $page) }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </li>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}"><i class="fas fa-arrow-right"></i></a></li>
            @else
                <li><a href="#"><i class="fas fa-arrow-right"></i></a></li>
            @endif
        </ul>
    </div>
@endif