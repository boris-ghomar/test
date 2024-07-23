@php
    $TableEnum = App\Enums\Database\Tables\NotificationsTableEnum::class;
    $idKey = $TableEnum::Id->dbName();
    $typeKey = $TableEnum::Type->dbName();

    $timestampsEnum = App\Enums\Database\Defaults\TimestampsEnum::class;
    $updatedAtKey = $timestampsEnum::UpdatedAt->dbName();
@endphp

@include('hhh.Site.pages.Notifications.Views.content_desktop')
@include('hhh.Site.pages.Notifications.Views.content_mobile')

@if (count($paginator) > 1)
    <button type="button" class="btn btn-danger btn-icon-text font-weight-bold"
        onclick="modalConfirmDeleteAllNotifications.create();">
        <i class="fa-solid fa-trash-can-list btn-icon-prepend"></i>
        @lang('general.buttons.DeleteALL')
    </button>

    <form method="POST" action="{{ SitePublicRoutesEnum::Notifications_DeleteALl->route() }}">
        @csrf
        <input type="hidden" name="_method" value="DELETE">

        <button id="DeleteAllNotifications" type="submit" class="d-none" onclick="modal_loading.show();">
            @lang('general.buttons.DeleteALL')
        </button>
    </form>
@endif
