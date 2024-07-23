@php
    /***************************** Notifications ******************************/
    $user = Auth::user();
    if ($user->isPersonnel()) {
        $notificationsRoute = App\Enums\Routes\AdminPublicRoutesEnum::Notifications->route();
    } else {
        $notificationsRoute = App\Enums\Routes\SitePublicRoutesEnum::Notifications->route();
    }

    $lastNotifications = $user->lastNotifications(3);

    $notificationsCounts = $user->notifications->count();
    $unreadNotificationsCounts = $user->unreadNotifications->count();

    /***************************** Notifications END ******************************/

@endphp

<li class="nav-item dropdown">
    <a class="nav-link count-indicator" id="notificationDropdown" href="#" data-bs-toggle="dropdown"
        aria-expanded="false">
        <i class="icon-bell"></i>

        @if ($unreadNotificationsCounts > 0)
            <span class="count"></span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0"
        aria-labelledby="notificationDropdown">
        <a class="dropdown-item py-3" href="{{ $notificationsRoute }}">
            <p class="mb-0 font-weight-medium float-left">@lang('bo_navbar.Notifications.unreadNotification', ['count' => $unreadNotificationsCounts, 'notification' => Str::of('notification')->plural($unreadNotificationsCounts)]) </p>
            <span class="badge badge-pill badge-primary float-right">@lang('bo_navbar.Notifications.viewAll')</span>
        </a>
        <div class="dropdown-divider"></div>

        @foreach ($lastNotifications as $notification)
            @php
                $notificationHandler = $notification->type;
                $subjectTextStyle = $notification->read_at === null ? 'text-white' : 'text-white-50';
            @endphp

            <a href="{{ $notificationsRoute }}" class="dropdown-item preview-item">
                {{-- iconView --}}
                <div class='preview-thumbnail'>
                    <div class='preview-icon {{ $notificationHandler::getIconBgClass() }}'>
                        <i class='{{ $notificationHandler::getIconViewClass() }}'></i>
                    </div>
                </div>

                {{-- iconView END --}}
                <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                    <span class="count"></span>
                    <h6 class="preview-subject {{ $subjectTextStyle }} fw-light small-text mb-1">
                        {{ $notificationHandler::getSubject() }}
                    </h6>

                    <p class="text-gray ellipsis mb-0"> {{ $notificationHandler::getMessage($notification->id) }} </p>
                </div>
            </a>
        @endforeach

    </div>
</li>
