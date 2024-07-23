@php

    $TicketsTableEnum = App\Enums\Database\Tables\TicketsTableEnum::class;
    $idKey = $TicketsTableEnum::Id->dbName();
    $subjectKey = $TicketsTableEnum::Subject->dbName();
    $statusKey = $TicketsTableEnum::Status->dbName();

    $TicketsStatusEnum = App\Enums\Tickets\TicketsStatusEnum::class;
@endphp

@include('hhh.Site.pages.Tickets.TicketsList.Views.content_desktop')
@include('hhh.Site.pages.Tickets.TicketsList.Views.content_mobile')
