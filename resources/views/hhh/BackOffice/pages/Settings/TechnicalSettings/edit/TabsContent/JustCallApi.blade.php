@include('hhh.BackOffice.pages.Settings.TechnicalSettings.edit.form-start')

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa fa-webhook"></i></span>
    <div class="media-body">
        <h4 class="tab-item-label mt-0">@lang('PagesContent_TechnicalSettings.tab.JustCallApi.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_TechnicalSettings.tab.JustCallApi.descriptionText')</p>
    </div>
</div>


{{-- JuCaAp_ApiName --}}
@php $attrName = AppTechnicalSettingsEnum::JuCaAp_ApiName->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- JuCaAp_ApiUrl --}}
@php $attrName = AppTechnicalSettingsEnum::JuCaAp_ApiUrl->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- JuCaAp_ApiKey --}}
@php $attrName = AppTechnicalSettingsEnum::JuCaAp_ApiKey->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- JuCaAp_ApiSecret --}}
@php $attrName = AppTechnicalSettingsEnum::JuCaAp_ApiSecret->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- JuCaAp_PhoneNumberForSMS --}}
@php $attrName = AppTechnicalSettingsEnum::JuCaAp_PhoneNumberForSMS->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'number',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction: ltr;',
])

{{-- Form END --}}
@include('hhh.BackOffice.pages.Settings.TechnicalSettings.edit.form-end')
