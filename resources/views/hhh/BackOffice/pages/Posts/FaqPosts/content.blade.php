@include('hhh.BackOffice.pages.general_structures.jsgrid_pages.content')


@can('create', App\Models\BackOffice\Posts\FaqPost::class)
    <div class="row grid-margin">
        <div class="col-12">
            <div class="card-body">

                <a type="button" class="btn btn-primary btn-icon-text font-weight-bold"
                    href="{{ AdminPublicRoutesEnum::Posts_FaqCreate->route() }}">
                    <i class="fa-solid fa-plus-large btn-icon-prepend"></i>
                    @lang('thisApp.Buttons.AddNewPost')
                </a>

            </div>
        </div>
    </div>
@endcan
