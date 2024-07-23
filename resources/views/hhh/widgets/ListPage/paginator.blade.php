@if ($paginator->hasPages())
    @php
        /* Config pager start and [START] */
        $pagerWrapCount = $pagerWrapCount ?? 2;

        $pagerCount = $pagerWrapCount * 2 + 1;
        $pageCount = $paginator->lastPage();

        $pagerStart = $paginator->currentPage() - $pagerWrapCount;
        $pagerEnd = $paginator->currentPage() + $pagerWrapCount;

        if ($pagerStart < 1) {
            $pagerStart = 1;
            $pagerEnd = $pagerCount > $pageCount ? $pageCount : $pagerCount;
        }
        if ($pagerEnd > $pageCount) {
            $pagerEnd = $pageCount;
            $pagerStart = $pagerEnd - $pagerCount + 1;
            $pagerStart = $pagerStart < 1 ? 1 : $pagerStart;
        }
        /* Config pager start and end [END]*/

        $lastPageLink = __('pagination.link', [
            'paginatorPath' => $paginator->path(),
            'paginatorPageName' => $paginator->getPageName(),
            'pageNumber' => $pageCount,
        ]);

    @endphp

    <div class="ms-3 mt-3">@lang('pagination.pages', ['count' => $pageCount])</div>

    <nav class="col-12">
        <ul class="pagination flex-wrap">



            {{-- <li class="me-3 mt-2">@lang('pagination.pages', ['count' => $pageCount])</li> --}}

            @if ($paginator->previousPageUrl())
                <li class="page-item"><a class="page-link" href="{{ $paginator->path() }}">@lang('pagination.first')</a></li>
                <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}">@lang('pagination.previous')</a>
                </li>
            @endif

            @for ($i = $pagerStart; $i <= $pagerEnd; $i++)
                @php
                    $pageLink =
                        $i == 1
                            ? $paginator->path()
                            : __('pagination.link', [
                                'paginatorPath' => $paginator->path(),
                                'paginatorPageName' => $paginator->getPageName(),
                                'pageNumber' => $i,
                            ]);
                @endphp

                @if ($i == $paginator->currentPage())
                    <li class="page-item  active"><a class="page-link">{{ $i }}</a></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $pageLink }}">{{ $i }}</a></li>
                @endif
            @endfor

            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}">@lang('pagination.next')</a>
                </li>
                <li class="page-item"><a class="page-link" href="{{ $lastPageLink }}">@lang('pagination.last')</a></li>
            @endif
        </ul>
    </nav>
@endif
