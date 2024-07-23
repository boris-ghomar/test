<div class="basic-table-desktop">
    <div class="row grid-margin">
        <div class="col-12">
            <div class="card">

                <div class="card-body basic-table-topic">

                    @if (count($paginator) < 1)
                        <div class="card-title">@lang('thisApp.Site.Tickets.NoTicket')</div>
                    @else
                        <table class="table mb-0">

                            <thead>
                                <tr>
                                    @foreach ($titles as $title)
                                        @if ($loop->index == 0 || $loop->index == 2 || $loop->index == 3)
                                            <th class="ps-0 text-center">{{ $title }}</th>
                                        @else
                                            <th class="ps-0">{{ $title }}</th>
                                        @endif
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($paginator as $ticket)
                                    @php
                                        $ticketStatusCase = $TicketsStatusEnum::getCase($ticket->$statusKey);
                                        $url = SitePublicRoutesEnum::Tickets_TicketShow->url(['myTicket' => $ticket->$idKey]);
                                    @endphp
                                    <tr onclick="window.location.href = '{{ $url }}'" class="basic-table-topic-link">
                                        <td class="ps-0 text-center" width="7%">{{ $ticket->$idKey }}</td>
                                        <td class="ps-0" width="23%">{{ $ticket->$subjectKey }}</td>
                                        <td class="ps-0 ltr text-center" width="20%">{{ $ticket->getLastUpdate() }}
                                        </td>
                                        <td class="ps-0 text-center" width="10%">
                                            <div class="badge {{ $ticketStatusCase->getbadge() }}">
                                                {{ $ticketStatusCase->translate() }}</div>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    @endif

                    @include('hhh.widgets.ListPage.paginator')
                </div>

            </div>
        </div>
    </div>
</div>
