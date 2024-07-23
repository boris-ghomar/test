@include('hhh.BackOffice.pages.Settings.TechnicalSettings.edit.form-start')

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa fa-badge-check"></i></span>
    <div class="media-body">
        <h4 class="tab-item-label mt-0">@lang('PagesContent_TechnicalSettings.tab.TrustScoreSystem.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_TechnicalSettings.tab.TrustScoreSystem.descriptionText')</p>
    </div>
</div>

{{-- TrScSy_NewClientBaseTrustScore --}}
@php $attrName = AppTechnicalSettingsEnum::TrScSy_NewClientBaseTrustScore->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'number',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- TrScSy_NegativePointValue --}}
@php $attrName = AppTechnicalSettingsEnum::TrScSy_NegativePointValue->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'number',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- TrScSy_DepositPerPoint --}}
@php $attrName = AppTechnicalSettingsEnum::TrScSy_DepositPerPoint->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'number',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- TrScSy_UsdPerPoint --}}
@php $attrName = AppTechnicalSettingsEnum::TrScSy_UsdPerPoint->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'number',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- TrScSy_IrtPerPoint --}}
@php $attrName = AppTechnicalSettingsEnum::TrScSy_IrtPerPoint->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'number',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- TrScSy_TomPerPoint --}}
@php $attrName = AppTechnicalSettingsEnum::TrScSy_TomPerPoint->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'number',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- TrScSy_IrrPerPoint --}}
@php $attrName = AppTechnicalSettingsEnum::TrScSy_IrrPerPoint->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'number',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])



{{-- Form END --}}
@include('hhh.BackOffice.pages.Settings.TechnicalSettings.edit.form-end')
