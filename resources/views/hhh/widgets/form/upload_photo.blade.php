{{--

    ========= Details params ==========
    @param \App\HHH_Library\general\php\FileAssistant $fileAssistant

    ========= File params ==========
    @param string $attrName
    @param string $label
    @param string $notice
    @param string $placeholder
    @param string $accept :accept files extentions
    @param bool $circlePreview ?? '': true | false -optinal- default: false
    @param bool $hideWidget ?? '': true | false -optinal- default: false

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

@php
    $fileConfig = $fileAssistant->getFileConfig();
@endphp
{{-- Photo Details --}}
<div id="{{ $attrName }}-widget" class="form-group @if ($hideWidget ?? '') d-none @endif">
    <label class="tab-item-label my-auto font-weight-bold" for="{{ $attrName }}">
        {{ $label }}
    </label>

    @if (!empty($notice))
        <label class="d-block col-sm-12 p-0"><span class='text-gray'>{!! $notice !!}</span></label>
    @endif

    <div class="media pb-4">

        @if ($circlePreview ?? '')
            <img style="width: 200px;height: 200px;" class="my-auto mx-3 rounded-circle"
                src="{{ $fileAssistant->getUrl() }}" />
        @else
            <img style="max-width:200px;max-height:90px;" class="my-auto mx-3" src="{{ $fileAssistant->getUrl() }}"
                alt="Profile Photo" />
        @endif

        <div class="media-body">
            <p>
                @lang('general.MinimumDimensions'):
                {{ $fileConfig->minWidth() }} *
                {{ $fileConfig->minHeight() }} @lang('general.Pixel')

                <br>
                @lang('general.MaximumDimensions'):
                {{ $fileConfig->maxWidth() }} *
                {{ $fileConfig->maxHeight() }} @lang('general.Pixel')

                <br>
                @lang('general.Size'):
                {{ $fileConfig->minSize() }} -
                {{ $fileConfig->maxSize() }} @lang('general.Kilobytes')

                <br>
                @lang('general.AllowedExtensions'):
                {{ $fileConfig->mimes() }}
            </p>
        </div>
    </div>

    {{-- Upload Photo --}}

    {{-- File Container Input --}}
    <input type="file" name="{{ $attrName }}" class="file-upload-default"
        accept="{{ $fileConfig->acceptableMimesForUpload() }}">

    <div class="input-group col-xs-12">

        {{-- file-upload-info --}}
        <input type="text" class="form-control file-upload-info h-auto" placeholder="{{ $placeholder }}" disabled>

        <span class="input-group-append">
            <button class="file-upload-browse btn btn-gradient-primary" type="button">@lang('general.buttons.ChooseFile')</button>
        </span>
    </div>
    @include('hhh.widgets.messages.ShowFieldAllErrors')
</div>

{{-- Upload Photo --}}
