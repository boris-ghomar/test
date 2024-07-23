<form class="forms-sample" action="{{ route(AdminRoutesEnum::Settings_GeneralSettings->value) }}" method="POST"
    enctype="multipart/form-data" onsubmit="modal_loading.show();">

    @csrf
    <input type="hidden" name="_method" value="PUT">
