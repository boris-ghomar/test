@include('hhh.BackOffice.pages.UserProfile.edit.form-start', ['tabpanel' => 'Photo'])

@php
    $fileConfig = constant('App\Enums\Resources\ImageConfigEnum::ProfilePhoto');
@endphp
{{-- media --}}
<div class="media pb-4 tab-photo">

    <img style="width: 200px;height: 200px;" class="mx-3 img-md rounded-circle" src="{{ $userProfile->photoUrl }}"
        alt="Profile Photo" />

    <div class="media-body">
        <h4 class="mt-0">@lang('PagesContent_PersonnelProfile.tab.Photo.descriptionTitle')</h4>
        <p>
            @lang('PagesContent_PersonnelProfile.tab.Photo.descriptionText')
            <br><br>
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


{{-- profile_photo_name --}}
@php $attrName = $UsersTableEnum::ProfilePhotoName->dbName(); @endphp
@include('hhh.widgets.form.upload_file', [
    'attrName' => $attrName,
    // 'label' => trans('PagesContent_PersonnelProfile.form.' . $attrName .'.name' ),
    'label' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_PersonnelProfile.form.notice.profile_photo_name' . $attrName),
    'placeholder' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.placeholder'),
    'accept' => $fileConfig->acceptableMimesForUpload(),
])

@include('hhh.BackOffice.pages.UserProfile.edit.form-end')
