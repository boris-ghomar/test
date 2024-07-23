@include('hhh.Site.pages.auth.Betconstruct.Registration.form-start', ['tabpanel' => 'FurtherInformation'])

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa-solid fa-square-info"></i></span>
    <div class="media-body">
        <h4 class="mt-0">@lang('PagesContent_RegisaterationBetconstruct.tab.FurtherInformation.descriptionTitle')</h4>
        <p class="text-justify">
            @lang('PagesContent_RegisaterationBetconstruct.tab.FurtherInformation.descriptionText')
        </p>
    </div>
</div>

{{-- gender --}}
@php $attrName = $ClientExtrasTableEnum::Gender->dbName(); @endphp
@if (in_array($attrName, $registrationFields))
    @include('hhh.widgets.form.dropdown', [
        'attrName' => $attrName,
        'label' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.name'),
        'notice' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.notice'),
        'collection' => $genderCollection,
        'selectedItem' => old($attrName),
    ])
@endif

{{-- birth_date_stamp --}}
@php $attrName = $ClientExtrasTableEnum::BirthDateStamp->dbName(); @endphp
@if (in_array($attrName, $registrationFields))
    @include('hhh.widgets.form.date-input-field', [
        'type' => 'text',
        'attrName' => $attrName,
        'label' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.name'),
        'notice' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.notice', [
            'calendarType' => $defalutCalendarName,
        ]),
        'placeholder' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.placeholder'),
        'value' => old($attrName),
    ])
@endif

{{-- ProvinceInternal --}}
@php $attrName = $ClientExtrasTableEnum::ProvinceInternal->dbName(); @endphp
@if (in_array($attrName, $registrationFields))
    @include('hhh.widgets.form.dropdown', [
        'attrName' => $attrName,
        'label' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.name'),
        'notice' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.notice'),
        'collection' => $provinceCollection,
        'selectedItem' => old($attrName),
    ])
@endif

{{-- CityInternal --}}
@php $attrName = $ClientExtrasTableEnum::CityInternal->dbName(); @endphp
@if (in_array($attrName, $registrationFields))
    @include('hhh.widgets.form.dropdown', [
        'attrName' => $attrName,
        'label' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.name'),
        'notice' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.notice'),
        'collection' => $citiesCollection,
        'selectedItem' => old($attrName),
        'hideWidget' => empty($citiesCollection),
    ])
@endif

{{--  contact_numbers_internal --}}
@php $attrName = $ClientExtrasTableEnum::ContactNumbersInternal->dbName(); @endphp
@if (in_array($attrName, $registrationFields))
    @include('hhh.widgets.form.input_group', [
        'attrName' => $attrName,
        'type' => 'number',
        'label' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.name'),
        'notice' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.notice'),
        'placeholder' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.placeholder'),
        'collection' => old($attrName, $contactNumbersCollection),
        'hideArrows' => true,
        'jsController' => 'contactNumbersInternalCtl',
    ])
@endif

{{-- contact_methods_internal --}}
@php $attrName = $ClientExtrasTableEnum::ContactMethodsInternal->dbName();@endphp
@if (in_array($attrName, $registrationFields))
    @include('hhh.widgets.form.checkbox_group', [
        'attrName' => $attrName,
        'label' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.name'),
        'notice' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.notice'),
        'collection' => $contactMethodsCollection,
        'selectedItemsList' => old($attrName),
    ])
@endif

{{-- caller_gender_internal --}}
@php $attrName = $ClientExtrasTableEnum::CallerGenderInternal->dbName(); @endphp
@if (in_array($attrName, $registrationFields))
    @include('hhh.widgets.form.checkbox_group', [
        'attrName' => $attrName,
        'label' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.name'),
        'notice' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.notice'),
        'collection' => $callerGenderCollection,
        'selectedItemsList' => old($attrName),
    ])
@endif

@include('hhh.Site.pages.auth.Betconstruct.Registration.form-end')
