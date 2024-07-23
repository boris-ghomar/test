@php
    $tableEnum = App\Enums\Database\Tables\PostsTableEnum::class;
    $mainPhotoUrl = $tableEnum::MainPhotoUrl->dbName();
    $shortenedContent = $tableEnum::ShortenedContentForPostSpace->dbName();
    $title = $tableEnum::Title->dbName();
    $isPinned = $tableEnum::IsPinned->dbName();
    $displayUrl = $tableEnum::DisplayUrl->dbName();
@endphp

@section('SearchGuideText')
    <div class="col-12 mb-2">
        <div class="text-muted ms-4 ps-3 pe-3 text-enter text-justify">{!! $SearchGuideText !!}</div>
    </div>
@endsection

<div class="row">
    <div class="col-lg-12">
        <div class="card link-list">

            <div class="card-body">
                <div class="col-12">
                    <form action="{{ SitePublicRoutesEnum::Search->route() }}">
                        <div class="form-group d-flex mb-0">
                            <input type="text" name="keyword" class="form-control" placeholder="@lang('thisApp.placeholder.SearchHere')"
                                value="{{ $keyword }}">
                            <button type="submit" class="btn btn-primary ms-3">@lang('thisApp.Buttons.Search')</button>
                        </div>
                    </form>
                </div>
            </div>


            @if (!is_null($paginator))
                <div class="card-body pt-0">
                    <div class="row">

                        <div class="col-12 mb-2">
                            <h1 class="title text-muted">@lang('thisApp.SearchResultsFor')<span>{{ $keyword }}</span></h1>
                            <p class="text-muted ms-3 ">{{ $searchResultInfo }}</p>
                        </div>

                        {{-- @yield('SearchGuideText') --}}

                        @foreach ($paginator->items() as $item)
                            <div class="col-12 results  @if ($item->$isPinned) pinned-post @endif">
                                @if ($item->$isPinned)
                                    <i class="fa-solid fa-thumbtack pin-icon"></i>
                                @endif
                                <div class="pt-4 @if (!$loop->last) border-bottom @endif">
                                    <a class="d-block h4" href="{{ $item->$displayUrl }}">
                                        <h2>{{ $item->$title }}</h2>
                                    </a>
                                    <p class="page-description mt-1 w-75 text-muted">{{ $item->$shortenedContent }}</p>
                                </div>
                            </div>
                        @endforeach

                    </div>
                    @include('hhh.widgets.ListPage.paginator')
                </div>
            @else
                <div class="card-body pt-0">
                    <div class="row">
                        @yield('SearchGuideText')
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
