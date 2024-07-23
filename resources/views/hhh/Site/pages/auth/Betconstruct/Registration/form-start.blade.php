<form id="{{ Str::snake($tabpanel) }}_form" class="forms-sample"
    action="{{ SitePublicRoutesEnum::RegisterBetconstruct->route() }}" method="POST" enctype="multipart/form-data"
    onsubmit="modal_loading.show();">

    @csrf

    <input type="hidden" name="_tabpanel" value="{{ $tabpanel }}">
