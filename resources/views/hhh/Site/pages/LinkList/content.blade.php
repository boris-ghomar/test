@php
    $tableEnum = App\Enums\Database\Tables\PostGroupsTableEnum::class;
    $title = $tableEnum::Title->dbName();
    $description = $tableEnum::Description->dbName();
    $displayUrl = $tableEnum::DisplayUrl->dbName();
@endphp

<div class="row">
    <div class="col-lg-12">
        <div class="card link-list">
            <div>
                <h1 class="title">{{ __('thisApp.linkList.pageTitle', ['title' => $pageTitle]) }}</h1>
            </div>
            <div class="card-body">
                <div class="row">

                    @foreach ($paginator->items() as $item)
                        <div class="col-12 results">
                            <div class="pt-4 @if (!$loop->last) border-bottom @endif">
                                <a class="d-block h4" href="{{ $item->$displayUrl }}"><h2>{{ $item->$title }}</h2></a>
                                <p class="page-description mt-1 w-75 text-muted">{{ $item->$description }}</p>
                            </div>
                        </div>
                    @endforeach

                </div>
                @include('hhh.widgets.ListPage.paginator')
            </div>
        </div>
    </div>
</div>
