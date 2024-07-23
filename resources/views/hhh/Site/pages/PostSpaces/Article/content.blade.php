@php
    $tableEnum = App\Enums\Database\Tables\PostsTableEnum::class;
    $mainPhotoUrl = $tableEnum::MainPhotoUrl->dbName();
    $shortenedContent = $tableEnum::ShortenedContentForPostSpace->dbName();
    $title = $tableEnum::Title->dbName();
    $isPinned = $tableEnum::IsPinned->dbName();
    $displayUrl = $tableEnum::DisplayUrl->dbName();
@endphp

<div class="row">
    <h1 class="title">{{ $pageTitle }}</h1>
    <div class="card-columns">

        @foreach ($paginator->items() as $item)
            <a href="{{ $item->$displayUrl }}" title="{{ $item->$title }}">
                <div class="card @if ($item->$isPinned) pinned-post @endif">
                    <img class="card-img-top" src="{{ $item->MainPhotoUrl }}" alt="{{ $item->$title }}"
                        title="{{ $item->$title }}">
                    <div class="card-body">
                        @if ($item->$isPinned)
                            <i class="fa-solid fa-thumbtack pin-icon"></i>
                        @endif
                        <h2 class="card-title mt-3">{{ $item->$title }}</h2>
                        <p class="card-text">{{ $item->$shortenedContent }}</p>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
    @include('hhh.widgets.ListPage.paginator')
</div>
