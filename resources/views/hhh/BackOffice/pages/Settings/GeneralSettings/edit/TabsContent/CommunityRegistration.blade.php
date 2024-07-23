@include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.form-start')

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa-regular fa-user-plus"></i></span>
    <div class="media-body">
        <h4 class="tab-item-label mt-0">@lang('PagesContent_GeneralSettings.tab.CommunityRegistration.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_GeneralSettings.tab.CommunityRegistration.descriptionText')</p>
    </div>
</div>

{{-- CommunityRegistrationIsActive --}}
@php $attrName = AppSettingsEnum::CommunityRegistrationIsActive->name; @endphp
@include('hhh.widgets.form.switch-btn', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'value' => $setting->$attrName,
])

{{-- CommunityRegistrationFields --}}
@php $attrName = AppSettingsEnum::CommunityRegistrationFields->name; @endphp
@include('hhh.widgets.form.checkbox_group', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'collection' => App\Enums\Users\ClientRegistrationAvailabelFieldsEnum::translatedArray(false),
    'selectedItemsList' => old($attrName, json_decode($setting->$attrName)),
    'collapse' => true,
    'useSelectButtons' => true,
])

{{-- CommunityRegistrationAvailableCurrencies --}}
@php $attrName = AppSettingsEnum::CommunityRegistrationAvailableCurrencies->name; @endphp
@include('hhh.widgets.form.checkbox_group', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'collection' => App\Enums\General\CurrencyEnum::getCollectionList(true, true, true),
    'selectedItemsList' => old($attrName, json_decode($setting->$attrName)),
    'collapse' => true,
    'useSelectButtons' => true,
])

{{-- CommunityRegistrationDefaultCurrency --}}
@php $attrName = AppSettingsEnum::CommunityRegistrationDefaultCurrency->name; @endphp
@include('hhh.widgets.form.dropdown', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'collection' => App\Enums\General\CurrencyEnum::getCollectionList(false, true, false),
    'selectedItem' => old($attrName, $setting->$attrName),
])

{{-- CommunityRegistrationTargetLinkAfterComplete --}}
@php $attrName = AppSettingsEnum::CommunityRegistrationTargetLinkAfterComplete->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
    'value' => old($attrName, $setting->$attrName),
    'style' => 'direction:ltr;',
])

{{-- Mobile Verificarion Settings --}}
<div class="form-group form-box mt-5">
    <label><i class="fa-solid fa-mobile-screen-button me-2 mb-2"></i> @lang('PagesContent_GeneralSettings.BoxLabels.CommunityRegistration.MobileVerificarionSettings')</label>
    <p class="mb-3">@lang('PagesContent_GeneralSettings.BoxLabels.CommunityRegistration.MobileVerificarionSettingsDescr')</p>

    {{-- CommunityRegistrationMobileVerificationIsRequired --}}
    @php $attrName = AppSettingsEnum::CommunityRegistrationMobileVerificationIsRequired->name; @endphp
    @include('hhh.widgets.form.switch-btn', [
        'attrName' => $attrName,
        'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
        'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
        'value' => old($attrName, $setting->$attrName),
    ])

    {{-- CommunityRegistrationMobileVerificationPerDay --}}
    @php $attrName = AppSettingsEnum::CommunityRegistrationMobileVerificationPerDay->name; @endphp
    @include('hhh.widgets.form.input-field', [
        'type' => 'number',
        'attrName' => $attrName,
        'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
        'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
        'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
        'value' => old($attrName, $setting->$attrName),
        'style' => 'direction:ltr;',
        'min' => 1,
    ])

    {{-- CommunityRegistrationMobileVerificationExpirationMinutes --}}
    @php $attrName = AppSettingsEnum::CommunityRegistrationMobileVerificationExpirationMinutes->name; @endphp
    @include('hhh.widgets.form.input-field', [
        'type' => 'number',
        'attrName' => $attrName,
        'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
        'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
        'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
        'value' => old($attrName, $setting->$attrName),
        'style' => 'direction:ltr;',
        'min' => 1,
    ])
    {{-- CommunityRegistrationMobileVerificationExpirationMinutesCoefficient --}}
    @php $attrName = AppSettingsEnum::CommunityRegistrationMobileVerificationExpirationMinutesCoefficient->name; @endphp
    @include('hhh.widgets.form.input-field', [
        'type' => 'number',
        'attrName' => $attrName,
        'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
        'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
        'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
        'value' => old($attrName, $setting->$attrName),
        'style' => 'direction:ltr;',
        'min' => '0.0',
    ])

    {{-- CommunityRegistrationMobileVerificationText --}}
    @php $attrName = AppSettingsEnum::CommunityRegistrationMobileVerificationText->name; @endphp
    @include('hhh.widgets.form.input-text_area-field', [
        'attrName' => $attrName,
        'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
        'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
        'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
        'value' => old($attrName, $setting->$attrName),
        'rows' => 5,
        'style' => 'resize: vertical;',
    ])
</div>

{{-- Email Verificarion Settings --}}
<div class="form-group form-box mt-5">
    <label><i class="fa-solid fa-envelope me-2 mb-2"></i> @lang('PagesContent_GeneralSettings.BoxLabels.CommunityRegistration.EmailVerificarionSettings')</label>
    <p class="mb-3">@lang('PagesContent_GeneralSettings.BoxLabels.CommunityRegistration.EmailVerificarionSettingsDescr')</p>

    {{-- CommunityRegistrationEmailVerificationIsRequired --}}
    @php $attrName = AppSettingsEnum::CommunityRegistrationEmailVerificationIsRequired->name; @endphp
    @include('hhh.widgets.form.switch-btn', [
        'attrName' => $attrName,
        'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
        'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
        'value' => $setting->$attrName,
    ])

    {{-- CommunityRegistrationEmailVerificationPerDay --}}
    @php $attrName = AppSettingsEnum::CommunityRegistrationEmailVerificationPerDay->name; @endphp
    @include('hhh.widgets.form.input-field', [
        'type' => 'number',
        'attrName' => $attrName,
        'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
        'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
        'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
        'value' => old($attrName, $setting->$attrName),
        'style' => 'direction:ltr;',
        'min' => 1,
    ])

    {{-- CommunityRegistrationEmailVerificationExpirationMinutes --}}
    @php $attrName = AppSettingsEnum::CommunityRegistrationEmailVerificationExpirationMinutes->name; @endphp
    @include('hhh.widgets.form.input-field', [
        'type' => 'number',
        'attrName' => $attrName,
        'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
        'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
        'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
        'value' => old($attrName, $setting->$attrName),
        'style' => 'direction:ltr;',
        'min' => 3,
    ])
    {{-- CommunityRegistrationEmailVerificationExpirationMinutesCoefficient --}}
    @php $attrName = AppSettingsEnum::CommunityRegistrationEmailVerificationExpirationMinutesCoefficient->name; @endphp
    @include('hhh.widgets.form.input-field', [
        'type' => 'number',
        'attrName' => $attrName,
        'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
        'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
        'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
        'value' => old($attrName, $setting->$attrName),
        'style' => 'direction:ltr;',
        'min' => '0.0',
    ])

    {{-- Disabled for now, do not need --}}
    {{-- CommunityRegistrationEmailVerificationText --}}
    {{-- @php $attrName = AppSettingsEnum::CommunityRegistrationEmailVerificationText->name; @endphp
    @include('hhh.widgets.form.input-text_area-field', [
        'attrName' => $attrName,
        'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
        'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
        'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
        'value' => old($attrName, $setting->$attrName),
        'rows' => 5,
        'style' => 'resize: vertical;',
    ]) --}}
</div>

@include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.form-end')
