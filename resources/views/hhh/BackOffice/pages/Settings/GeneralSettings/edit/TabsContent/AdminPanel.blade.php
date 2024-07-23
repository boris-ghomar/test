@include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.form-start')

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa fa-building"></i></span>
    <div class="media-body">
        <h4 class="tab-item-label mt-0">@lang('PagesContent_GeneralSettings.tab.AdminPanel.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_GeneralSettings.tab.AdminPanel.descriptionText')</p>
    </div>
</div>

{{-- IsAdminPanelActive --}}
@php $attrName = AppSettingsEnum::IsAdminPanelActive->name; @endphp
@php $isAdminPanelActive = $setting->$attrName; @endphp
@include('hhh.widgets.form.switch-btn', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'value' => $setting->$attrName,
    'onClick' => sprintf(
        "toggleElementClass('%s-widget','d-none');",
        AppSettingsEnum::AdminPanelExplanationInactive->name),
])

{{-- AdminPanelExplanationInactive --}}
@php $attrName = AppSettingsEnum::AdminPanelExplanationInactive->name; @endphp
@include('hhh.widgets.form.input-text_area-field', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'rows' => 10,
    'hideWidget' => $isAdminPanelActive,
    'style' => "resize: vertical;",
])


{{-- AdminPanelTimeZone --}}
@php $attrName = AppSettingsEnum::AdminPanelTimeZone->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- canPersonnelChangeTimeZone --}}
@php $attrName = AppSettingsEnum::canPersonnelChangeTimeZone->name; @endphp
@include('hhh.widgets.form.switch-btn', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'value' => $setting->$attrName,
])

{{-- AdminPanelCalendarType --}}
@php $attrName = AppSettingsEnum::AdminPanelCalendarType->name; @endphp
@include('hhh.widgets.form.dropdown', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
    'collection' => $calendarTypeCollection,
    'selectedItem' => $setting->$attrName,
])

{{-- canPersonnelChangeCalendarType --}}
@php $attrName = AppSettingsEnum::canPersonnelChangeCalendarType->name; @endphp
@include('hhh.widgets.form.switch-btn', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'value' => $setting->$attrName,
])


{{-- AdminPanelDefaultLanguage --}}
@php $attrName = AppSettingsEnum::AdminPanelDefaultLanguage->name; @endphp
@include('hhh.widgets.form.dropdown', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
    'collection' => $languageCollection,
    'selectedItem' => $setting->$attrName,
])

{{-- AdminPanelBigLogo --}}
@php $attrName = AppSettingsEnum::AdminPanelBigLogo->name; @endphp
@include('hhh.widgets.form.upload_photo', [
    'fileAssistant' => $files[$attrName],
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
])

{{-- AdminPanelMiniLogo --}}
@php $attrName = AppSettingsEnum::AdminPanelMiniLogo->name; @endphp
@include('hhh.widgets.form.upload_photo', [
    'fileAssistant' => $files[$attrName],
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
])

{{-- AdminPanelFavicon --}}
@php $attrName = AppSettingsEnum::AdminPanelFavicon->name; @endphp
@include('hhh.widgets.form.upload_photo', [
    'fileAssistant' => $files[$attrName],
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
])




{{-- Form END --}}
@include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.form-end')
