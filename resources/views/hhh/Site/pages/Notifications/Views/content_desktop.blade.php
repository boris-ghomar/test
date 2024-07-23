<div class="basic-table-desktop">
    <div class="row grid-margin">
        <div class="col-12">
            <div class="card">

                <div class="card-body basic-table-topic">
                    @if (count($paginator) < 1)
                        <div class="card-title">@lang('thisApp.Site.Notifications.EmptyList')</div>
                    @else
                        <table class="table mb-0">

                            <thead>
                                <tr>
                                    @foreach ($titles as $title)
                                        @if ($loop->index == 0 || $loop->index == 3 || $loop->index == 4)
                                            <th class="ps-0 text-center">{{ $title }}</th>
                                        @else
                                            <th class="ps-0">{{ $title }}</th>
                                        @endif
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($paginator as $notification)
                                    @php
                                        $notificationHandler = $notification->$typeKey;
                                        $subject = $notificationHandler::getSubject();
                                        $message = $notificationHandler::getMessage($notification->$idKey);
                                    @endphp
                                    {{-- <tr onclick="window.location.href = '{{ $url }}'" class="basic-table-topic-link"> --}}
                                    <tr>
                                        <td class="ps-0 text-center ltr" width="10%">
                                            {{ $notification->$updatedAtKey }}</td>
                                        <td class="ps-0" width="25%">{{ $subject }}</td>
                                        <td class="ps-0" width="65%">{!! $message !!}</td>
                                        <td class="ps-0 text-center" width="10%">
                                            <form method="POST"
                                                action="{{ SitePublicRoutesEnum::Notifications_Delete->route() }}">
                                                @csrf
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="id"
                                                    value="{{ $notification->$idKey }}">

                                                <button type="submit" class="btn" onclick="modalLoading.show();">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>

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
