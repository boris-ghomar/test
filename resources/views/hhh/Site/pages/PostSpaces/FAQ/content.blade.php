@php
    $tableEnum = App\Enums\Database\Tables\PostsTableEnum::class;
    $shortenedContent = $tableEnum::ShortenedContentForPostSpace->dbName();
    $title = $tableEnum::Title->dbName();
    $isPinned = $tableEnum::IsPinned->dbName();
    $displayUrl = $tableEnum::DisplayUrlFaq->dbName();
@endphp

<div class="row">
    <div class="card">
        <div class="faq-block card-body">
            <div class="container-fluid py-2">
                <h1 class="title">{{ $pageTitle }}</h1>
            </div>
            <div class="accordion">

                @foreach ($paginator->items() as $item)
                    @php
                        $loopIndex = $loop->iteration;
                        $isFirstItem = $loopIndex === 1;
                    @endphp

                    <div class="card @if ($item->$isPinned) pinned-post @endif">
                        @if ($item->$isPinned)
                            <i class="fa-solid fa-thumbtack pin-icon"></i>
                        @endif
                        <div class="card-header" id="heading_{{ $loopIndex }}">
                            <h2 class="mb-0">
                                <a data-bs-toggle="collapse" data-bs-target="#collapse_{{ $loopIndex }}"
                                    aria-expanded="{{ $isFirstItem ? 'true' : 'false' }}"
                                    aria-controls="collapse_{{ $loopIndex }}">
                                    {{ $item->$title }}
                                </a>
                            </h2>
                        </div>
                        <div id="collapse_{{ $loopIndex }}"
                            class="collapse @if ($isFirstItem) show @endif"
                            aria-labelledby="heading_{{ $loopIndex }}"
                            data-bs-parent="#accordion{{ $loopIndex }}">
                            <div class="card-body">
                                <p class="mb-0">{!! $item->$shortenedContent !!}</p>
                                <p class="mt-3"><a href="{{ $item->$displayUrl }}"
                                        title="{{ $item->$title }}">@lang('thisApp.ReadMore')</a></p>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
            @include('hhh.widgets.ListPage.paginator')
        </div>
    </div>
</div>
