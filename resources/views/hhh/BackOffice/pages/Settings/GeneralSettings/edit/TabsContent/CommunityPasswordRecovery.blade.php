@include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.form-start')

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa-regular fa-user-magnifying-glass"></i></span>
    <div class="media-body">
        <h4 class="tab-item-label mt-0">@lang('PagesContent_GeneralSettings.tab.CommunityPasswordRecovery.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_GeneralSettings.tab.CommunityPasswordRecovery.descriptionText')</p>
    </div>
</div>

{{-- CommunityPasswordRecoveryIsActive --}}
@php $attrName = AppSettingsEnum::CommunityPasswordRecoveryIsActive->name; @endphp
@include('hhh.widgets.form.switch-btn', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'value' => $setting->$attrName,
])

{{-- CommunityPasswordRecoveryMethods --}}
@php $attrName = AppSettingsEnum::CommunityPasswordRecoveryMethods->name; @endphp
@include('hhh.widgets.form.checkbox_group', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'collection' => App\Enums\Users\PasswordRecoveryMethodEnum::translatedArray(false),
    'selectedItemsList' => old($attrName, json_decode($setting->$attrName)),
    'collapse' => true,
    'useSelectButtons' => true,
])

{{-- CommunityPasswordRecoveryDefaultMethod --}}
@php $attrName = AppSettingsEnum::CommunityPasswordRecoveryDefaultMethod->name; @endphp
@include('hhh.widgets.form.dropdown', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'collection' => App\Enums\Users\PasswordRecoveryMethodEnum::translatedArray(true),
    'selectedItem' => old($attrName, $setting->$attrName),
])

{{-- Mobile Verificarion Settings --}}
<div class="form-group form-box mt-5">
    <label><i class="fa-solid fa-mobile-screen-button me-2 mb-2"></i> @lang('PagesContent_GeneralSettings.BoxLabels.CommunityPasswordRecovery.MobileVerificarionSettings')</label>
    <p class="mb-3">@lang('PagesContent_GeneralSettings.BoxLabels.CommunityPasswordRecovery.MobileVerificarionSettingsDescr')</p>

    {{-- CommunityPasswordRecoveryMobileVerificationPerDay --}}
    @php $attrName = AppSettingsEnum::CommunityPasswordRecoveryMobileVerificationPerDay->name; @endphp
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

    {{-- CommunityPasswordRecoveryMobileVerificationExpirationMinutes --}}
    @php $attrName = AppSettingsEnum::CommunityPasswordRecoveryMobileVerificationExpirationMinutes->name; @endphp
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

    {{-- CommunityPasswordRecoveryMobileVerificationText --}}
    @php $attrName = AppSettingsEnum::CommunityPasswordRecoveryMobileVerificationText->name; @endphp
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
    <label><i class="fa-solid fa-envelope me-2 mb-2"></i> @lang('PagesContent_GeneralSettings.BoxLabels.CommunityPasswordRecovery.EmailVerificarionSettings')</label>
    <p class="mb-3">@lang('PagesContent_GeneralSettings.BoxLabels.CommunityPasswordRecovery.EmailVerificarionSettingsDescr')</p>


    {{-- CommunityPasswordRecoveryEmailVerificationPerDay --}}
    @php $attrName = AppSettingsEnum::CommunityPasswordRecoveryEmailVerificationPerDay->name; @endphp
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

    {{-- CommunityPasswordRecoveryEmailVerificationExpirationMinutes --}}
    @php $attrName = AppSettingsEnum::CommunityPasswordRecoveryEmailVerificationExpirationMinutes->name; @endphp
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

</div>

@include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.form-end')
