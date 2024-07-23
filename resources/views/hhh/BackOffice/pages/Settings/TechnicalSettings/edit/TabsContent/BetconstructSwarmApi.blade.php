@include('hhh.BackOffice.pages.Settings.TechnicalSettings.edit.form-start')

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa fa-webhook"></i></span>
    <div class="media-body">
        <h4 class="tab-item-label mt-0">@lang('PagesContent_TechnicalSettings.tab.BetconstructSwarmApi.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_TechnicalSettings.tab.BetconstructSwarmApi.descriptionText')</p>
    </div>
</div>


{{-- BcSwAp_ApiName --}}
@php $attrName = AppTechnicalSettingsEnum::BcSwAp_ApiName->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- BcSwAp_ApiUrl --}}
@php $attrName = AppTechnicalSettingsEnum::BcSwAp_ApiUrl->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- BcSwAp_WebSocketUrl --}}
@php $attrName = AppTechnicalSettingsEnum::BcSwAp_WebSocketUrl->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- BcSwAp_WebSocketUrlAlternative --}}
@php $attrName = AppTechnicalSettingsEnum::BcSwAp_WebSocketUrlAlternative->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- BcSwAp_SiteId --}}
@php $attrName = AppTechnicalSettingsEnum::BcSwAp_SiteId->name; @endphp
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
