<div class="basic-table-mobile">
    @foreach ($paginator as $ticket)
        <div class="row grid-margin">
            <div class="col-12">
                <div class="card">

                    <div class="card-body basic-table-topic">

                        @if (count($paginator) < 1)
                            <div class="card-title">@lang('thisApp.Tickets.NoTicket')</div>
                        @else
                            @php
                                $ticketStatusCase = $TicketsStatusEnum::getCase($ticket->$statusKey);
                                $url = SitePublicRoutesEnum::Tickets_TicketShow->url(['myTicket' => $ticket->$idKey]);
                            @endphp

                            <table class="table mb-0" onclick="window.location.href = '{{ $url }}'"
                                class="basic-table-topic-link">

                                <tbody>

                                    <tr>
                                        <td class="ps-0">{{ $titles[0] }}</td>
                                        <td class="ps-0 text-end">{{ $ticket->$idKey }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0">{{ $titles[1] }}</td>
                                        <td class="ps-0 text-end">{{ $ticket->$subjectKey }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0">{{ $titles[2] }}</td>
                                        <td class="ps-0 ltr" style="text-align: start;">{{ $ticket->getLastUpdate() }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0">{{ $titles[3] }}</td>
                                        <td class="ps-0 text-end align-middle">
                                            <div class="badge {{ $ticketStatusCase->getbadge() }}">
                                                {{ $ticketStatusCase->translate() }}</div>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        @endif


                    </div>

                </div>
            </div>
        </div>
    @endforeach

    @include('hhh.widgets.ListPage.paginator')
</div>
