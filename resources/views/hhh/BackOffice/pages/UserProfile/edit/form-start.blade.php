<form class="forms-sample" action="{{ AdminPublicRoutesEnum::Profile->route() }}" method="POST"
    enctype="multipart/form-data" onsubmit="modal_loading.show();">

    @csrf
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="_tabpanel" value="{{ $tabpanel }}">
