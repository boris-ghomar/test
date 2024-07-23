@php
$tableEnum = App\Enums\Database\Tables\PostsTableEnum::class;
$mainPhotoUrl = $tableEnum::MainPhotoUrl->dbName();
$shortenedContent = $tableEnum::ShortenedContentForPostSpace->dbName();
$title = $tableEnum::Title->dbName();
$isPinned = $tableEnum::IsPinned->dbName();
$displayUrl = $tableEnum::DisplayUrl->dbName();
@endphp

<h1 style="
    font-size: 0pt;
    height:0px;
">باشگاه مشتریان بتکارت</h1>
<div class="alert alert-success text-center" role="alert">
    @if (Auth::check())
    @lang('thisApp.GetBetcartLink')
    <br>
    <a type="button" class="btn btn-primary btn-icon-text font-weight-bold mt-3" href="{{ SitePublicRoutesEnum::IpRestrictionRedirect->url() }}" target="_parent">
        {{-- <i class="mdi mdi-logout btn-icon-prepend"></i> --}}
        @lang('thisApp.Buttons.GetBetcartAddress')
    </a>

    @else
    @lang('thisApp.GoToBetcart')
    <br>
    <a type="button" class="btn btn-primary btn-icon-text font-weight-bold mt-3 w-full" href="https://www.betcart.com" target="_parent">
        {{-- <i class="mdi mdi-logout btn-icon-prepend"></i> --}}
        @lang('thisApp.Buttons.GoToBetcart')
    </a>

    @endif
</div>


<div class="row">
    <div class="card-columns">

        @foreach ($paginator->items() as $item)
        <a href="{{ $item->$displayUrl }}" title="{{ $item->$title }}">
            <div class="card @if ($item->$isPinned) pinned-post @endif">

                <img class="card-img-top sm:max-w-64" src="{{ $item->MainPhotoUrl }}" alt="{{ $item->$title }}" title="{{ $item->$title }}">
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