<div class="basic-table-mobile">
    @if (count($paginator) < 1)
        <div class="card-title">@lang('thisApp.Site.Notifications.EmptyList')</div>
    @endif

    @foreach ($paginator as $notification)
        @php
            $notificationHandler = $notification->$typeKey;
            $subject = $notificationHandler::getSubject();
            $message = $notificationHandler::getMessage($notification->$idKey);
        @endphp

        <div class="row grid-margin">
            <div class="col-12">
                <div class="card">

                    <div class="card-body basic-table-topic">

                        <table class="table mb-0" class="basic-table-topic-link">

                            <tbody>

                                <tr>
                                    <td class="ps-0">{{ $titles[0] }}</td>
                                    <td class="ps-0 ltr" style="text-align: start;">
                                        {{ $notification->$updatedAtKey }}</td>
                                </tr>
                                <tr>
                                    <td class="ps-0">{{ $titles[1] }}</td>
                                    <td class="ps-0 text-end">{{ $subject }}</td>
                                </tr>
                                <tr>
                                    <td class="ps-0">{{ $titles[2] }}</td>
                                    <td class="ps-0 text-end">{!! $message !!}</td>
                                </tr>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    @endforeach



    @include('hhh.widgets.ListPage.paginator')
</div>
