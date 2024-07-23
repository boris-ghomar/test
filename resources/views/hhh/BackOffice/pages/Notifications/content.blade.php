@include('hhh.BackOffice.pages.general_structures.jsgrid_pages.content')

@if ($userHasNotification)
    <div class="row grid-margin">
        <div class="col-12">
            <div class="card-body">

                <button type="button" class="btn btn-danger btn-icon-text font-weight-bold"
                    onclick="modalConfirmDeleteAllNotifications.create();">
                    <i class="fa-solid fa-trash-can-list btn-icon-prepend"></i>
                    @lang('general.buttons.DeleteALL')
                </button>

                <form method="POST" action="{{ App\Enums\Routes\AdminPublicRoutesEnum::Notifications->route() }}">
                    @csrf
                    <input type="hidden" name="_method" value="DELETE">

                    <button id="DeleteAllNotifications" type="submit" class="d-none" onclick="modal_loading.show();">
                        @lang('general.buttons.DeleteALL')
                    </button>
                </form>

            </div>
        </div>
    </div>
@endif
