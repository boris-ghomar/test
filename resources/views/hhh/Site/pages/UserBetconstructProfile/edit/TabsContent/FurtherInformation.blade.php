@include('hhh.Site.pages.UserBetconstructProfile.edit.form-start', ['tabpanel' => 'FurtherInformation'])

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa-solid fa-square-info"></i></span>
    <div class="media-body">
        <h4 class="mt-0">@lang('PagesContent_UserBetconstructProfile.tab.FurtherInformation.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_UserBetconstructProfile.tab.FurtherInformation.descriptionText')</p>
        @if (!$isFurtherInformationTabCompleted)
            <p class="text-justify text-danger"><i
                    class="fa-solid fa-circle-exclamation text-danger me-2"></i>@lang('PagesContent_UserBetconstructProfile.tab.FurtherInformation.incompleteInformation')</p>
        @endif
    </div>
</div>

{{-- gender --}}
@php $attrName = $ClientExtrasTableEnum::Gender->dbName(); @endphp
@include('hhh.widgets.form.dropdown', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice'),
    'collection' => $genderCollection,
    'selectedItem' => old($attrName, $userProfileExtra->$attrName),
])

{{-- birth_date_stamp --}}
@php $attrName = $ClientExtrasTableEnum::BirthDateStamp->dbName(); @endphp
@if(empty($userProfileExtra->$attrName))
@include('hhh.widgets.form.date-input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice', [
        'calendarType' => $userProfile->getCalendarType()->translate(),
    ]),
    'placeholder' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.placeholder'),
    'value' => $userProfileExtra->$attrName,
])
@endif

{{-- ProvinceInternal --}}
@php $attrName = $ClientExtrasTableEnum::ProvinceInternal->dbName(); @endphp
@include('hhh.widgets.form.dropdown', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice'),
    'collection' => $provinceCollection,
    'selectedItem' => old($attrName, $userProfileExtra->$attrName),
])

{{-- CityInternal --}}
@php $attrName = $ClientExtrasTableEnum::CityInternal->dbName(); @endphp
@include('hhh.widgets.form.dropdown', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice'),
    'collection' => $citiesCollection,
    'selectedItem' => old($attrName, $userProfileExtra->$attrName),
    'hideWidget' => empty($citiesCollection),
])

{{--  ContactNumbersInternal --}}
@php $attrName = $ClientExtrasTableEnum::ContactNumbersInternal->dbName(); @endphp
@include('hhh.widgets.form.input_group', [
    'attrName' => $attrName,
    'type' => 'number',
    'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.placeholder'),
    'collection' => old($attrName, $userProfileExtra->$attrName),
    'hideArrows' => true,
    'jsController' => 'contactNumbersInternalCtl',
])

{{-- contact_methods_internal --}}
@php $attrName = $ClientExtrasTableEnum::ContactMethodsInternal->dbName();@endphp
@include('hhh.widgets.form.checkbox_group', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice'),
    'collection' => $contactMethodsCollection,
    'selectedItemsList' => old($attrName, $userProfileExtra->$attrName),
])

{{-- caller_gender_internal --}}
@php $attrName = $ClientExtrasTableEnum::CallerGenderInternal->dbName(); @endphp
@include('hhh.widgets.form.checkbox_group', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice'),
    'collection' => $callerGenderCollection,
    'selectedItemsList' => old($attrName, $userProfileExtra->$attrName),
])

{{-- JobFieldInternal --}}
@php $attrName = $ClientExtrasTableEnum::JobFieldInternal->dbName(); @endphp
@include('hhh.widgets.form.dropdown', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice'),
    'collection' => $jobFieldsCollection,
    'selectedItem' => old($attrName, $userProfileExtra->$attrName),
])

{{-- iban --}}
@php $attrName = $ClientExtrasTableEnum::IBAN->dbName(); @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'number',
    'attrName' => $attrName,
    'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.placeholder'),
    'value' => $userProfileExtra->$attrName,
    'hideArrows' => true,
    'style' => 'direction: ltr; text-align: left;',
])

@include('hhh.Site.pages.UserBetconstructProfile.edit.form-end')
