@include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.form-start')

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa fa-balance-scale"></i></span>
    <div class="media-body">
        <h4 class="tab-item-label mt-0">@lang('PagesContent_GeneralSettings.tab.AppRules.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_GeneralSettings.tab.AppRules.descriptionText')</p>
    </div>
</div>


{{-- TermsAndConditions --}}
@php $attrName = AppSettingsEnum::TermsAndConditions->name; @endphp
@include('hhh.widgets.form.input-text_area-field', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'rows' => 30,
    'style' => 'resize:vertical;',
])

@include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.form-end')
