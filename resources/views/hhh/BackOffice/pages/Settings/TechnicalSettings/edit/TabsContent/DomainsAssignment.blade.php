@include('hhh.BackOffice.pages.Settings.TechnicalSettings.edit.form-start')

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa fa-brands fa-internet-explorer"></i></span>
    <div class="media-body">
        <h4 class="tab-item-label mt-0">@lang('PagesContent_TechnicalSettings.tab.DomainsAssignment.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_TechnicalSettings.tab.DomainsAssignment.descriptionText')</p>
    </div>
</div>


{{-- DoAsSy_PermanentDomain --}}
@php $attrName = AppTechnicalSettingsEnum::DoAsSy_PermanentDomain->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- DoAsSy_MinReportCount --}}
@php $attrName = AppTechnicalSettingsEnum::DoAsSy_MinReportCount->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'number',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- DoAsSy_MinAssignableTrustScore --}}
@php $attrName = AppTechnicalSettingsEnum::DoAsSy_MinAssignableTrustScore->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'number',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- DoAsSy_MaxAssignableDomains --}}
@php $attrName = AppTechnicalSettingsEnum::DoAsSy_MaxAssignableDomains->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'number',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- DoAsSy_MinPublicDomainReportsCount --}}
@php $attrName = AppTechnicalSettingsEnum::DoAsSy_MinPublicDomainReportsCount->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'number',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- DoAsSy_MinPublicDomainHoldMinutes --}}
@php $attrName = AppTechnicalSettingsEnum::DoAsSy_MinPublicDomainHoldMinutes->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'number',
    'attrName' => $attrName,
    'label' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_TechnicalSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- DoAsSy_DaysOfKeepingExipredAssignments --}}
@php $attrName = AppTechnicalSettingsEnum::DoAsSy_DaysOfKeepingExipredAssignments->name; @endphp
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
