@include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.form-start')

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa-solid fa-money-check-dollar"></i></span>
    <div class="media-body">
        <h4 class="tab-item-label mt-0">@lang('PagesContent_GeneralSettings.tab.Bet.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_GeneralSettings.tab.Bet.descriptionText')</p>
    </div>
</div>


{{-- BetDaysOfKeepingHistory --}}
@php $attrName = AppSettingsEnum::BetDaysOfKeepingHistory->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'number',
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
    'min' => 30,
    'max' => 100,

])


{{-- Form END --}}
@include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.form-end')
