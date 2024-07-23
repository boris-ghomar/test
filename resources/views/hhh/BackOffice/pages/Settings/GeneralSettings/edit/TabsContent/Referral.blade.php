@include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.form-start')

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa-duotone fa-person-sign"></i></span>
    <div class="media-body">
        <h4 class="tab-item-label mt-0">@lang('PagesContent_GeneralSettings.tab.Referral.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_GeneralSettings.tab.Referral.descriptionText1')</p>
        <p class="text-justify">@lang('PagesContent_GeneralSettings.tab.Referral.descriptionText2')</p>
    </div>
</div>


{{-- ReferralIsActive --}}
@php $attrName = AppSettingsEnum::ReferralIsActive->name; @endphp
@include('hhh.widgets.form.switch-btn', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'value' => $setting->$attrName,
])

{{-- ReferralIsActiveForTestClients --}}
@php $attrName = AppSettingsEnum::ReferralIsActiveForTestClients->name; @endphp
@include('hhh.widgets.form.switch-btn', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'value' => $setting->$attrName,
])

{{-- ReferralAutoRenewLastSession --}}
@php $attrName = AppSettingsEnum::ReferralAutoRenewLastSession->name; @endphp
@include('hhh.widgets.form.switch-btn', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'value' => $setting->$attrName,
])

{{-- ReferralPageNote --}}
@php $attrName = AppSettingsEnum::ReferralPageNote->name; @endphp
@include('hhh.widgets.form.input-text_area-field', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
    'value' => old($attrName, $setting->$attrName),
    'rows' => 10,
    'style' => 'resize: vertical;',
])


{{-- Form END --}}
@include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.form-end')
