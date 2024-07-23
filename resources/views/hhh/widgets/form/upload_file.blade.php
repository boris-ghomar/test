{{--
    @param $attrName
    @param $label
    @param $notice
    @param $placeholder
    @param $accept :accept files extentions
    @param $hideWidget ?? '': true | false -optinal- default: false

--}}

{{-- Example: --}}

{{--

@php $attrName = "profile_photo_name"; @endphp
@include('hhh.widgets.form.upload_file',
[
'attrName' => $attrName,
'label' => trans('PagesContent_PersonnelProfile.form.' . $attrName .'.name' ),
'notice' => trans('PagesContent_PersonnelProfile.form.' . $attrName .'.notice' ),
'placeholder' => trans('PagesContent_PersonnelProfile.form.' . $attrName .'.placeholder' ),
'accept' => $user_profile->getPhotoFileConfig()->acceptableMimesForUpload(),
]
)

--}}


<div id="{{ $attrName }}-widget" class="form-group @if ($hideWidget ?? '') d-none @endif">
    <label class="tab-item-label my-auto font-weight-bold" for="{{ $attrName }}">
        {{ $label }}
    </label>

    {{-- File Container Input --}}
    <input type="file" name="{{ $attrName }}" class="file-upload-default" accept="{{ $accept }}">

    <div class="input-group col-xs-12">

        {{-- file-upload-info --}}
        <input type="text" class="form-control file-upload-info h-auto" placeholder="{{ $placeholder }}"
            disabled="">
        <span class="input-group-append">
            <button class="file-upload-browse btn btn-gradient-primary" type="button">@lang('general.buttons.ChooseFile')</button>
        </span>
    </div>
    @include('hhh.widgets.messages.ShowFieldAllErrors')
</div>
