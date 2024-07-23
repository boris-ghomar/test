@include('hhh.BackOffice.pages.Settings.TechnicalSettings.edit.form-start')

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa fa-webhook"></i></span>
    <div class="media-body">
        <h4 class="tab-item-label mt-0">@lang('PagesContent_TechnicalSettings.tab.BetconstructExternalAdmin.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_TechnicalSettings.tab.BetconstructExternalAdmin.descriptionText')</p>
    </div>
</div>


{{-- BcExAd_ApiName --}}
@php $attrName = AppTechnicalSettingsEnum::BcExAd_ApiName->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- BcExAd_HashAlgorithm --}}
@php $attrName = AppTechnicalSettingsEnum::BcExAd_HashAlgorithm->name; @endphp
@php $defValue = AppTechnicalSettingsEnum::BcExAd_HashAlgorithm->defaultValue(); @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice',['default' => $defValue]),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- BcExAd_ApiUrl --}}
@php $attrName = AppTechnicalSettingsEnum::BcExAd_ApiUrl->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- BcExAd_PartnerId --}}
@php $attrName = AppTechnicalSettingsEnum::BcExAd_PartnerId->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- BcExAd_HashKey --}}
@php $attrName = AppTechnicalSettingsEnum::BcExAd_HashKey->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- Form END --}}
@include('hhh.BackOffice.pages.Settings.TechnicalSettings.edit.form-end')
