@include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.form-start')

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa-duotone fa-people-group"></i></span>
    <div class="media-body">
        <h4 class="tab-item-label mt-0">@lang('PagesContent_GeneralSettings.tab.Community.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_GeneralSettings.tab.Community.descriptionText')</p>
    </div>
</div>

{{-- IsCommunityActive --}}
@php $attrName = AppSettingsEnum::IsCommunityActive->name; @endphp
@php $isCommunityActive = $setting->$attrName; @endphp
@include('hhh.widgets.form.switch-btn', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'value' => $setting->$attrName,
    'onClick' => sprintf(
        "toggleElementClass('%s-widget','d-none');",
        AppSettingsEnum::CommunityExplanationInactive->name),
])

{{-- CommunityExplanationInactive --}}
@php $attrName = AppSettingsEnum::CommunityExplanationInactive->name; @endphp
@include('hhh.widgets.form.input-text_area-field', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'rows' => 10,
    'hideWidget' => $isCommunityActive,
    'style' => 'resize: vertical;',
])

{{-- CommunityTimeZone --}}
@php $attrName = AppSettingsEnum::CommunityTimeZone->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'style' => 'direction:ltr;',
])

{{-- canClientChangeTimeZone --}}
@php $attrName = AppSettingsEnum::canClientChangeTimeZone->name; @endphp
@include('hhh.widgets.form.switch-btn', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'value' => $setting->$attrName,
])

{{-- CommunityCalendarType --}}
@php $attrName = AppSettingsEnum::CommunityCalendarType->name; @endphp
@include('hhh.widgets.form.dropdown', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
    'collection' => $calendarTypeCollection,
    'selectedItem' => $setting->$attrName,
])


{{-- canClientChangeCalendarType --}}
@php $attrName = AppSettingsEnum::canClientChangeCalendarType->name; @endphp
@include('hhh.widgets.form.switch-btn', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'value' => $setting->$attrName,
])

{{-- CommunityDefaultLanguage --}}
@php $attrName = AppSettingsEnum::CommunityDefaultLanguage->name; @endphp
@include('hhh.widgets.form.dropdown', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
    'collection' => $languageCollection,
    'selectedItem' => $setting->$attrName,
])

{{-- CommentApproval --}}
@php $attrName = AppSettingsEnum::CommentApproval->name; @endphp
@include('hhh.widgets.form.switch-btn', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'value' => $setting->$attrName,
])

{{-- SupportEmail --}}
@php $attrName = AppSettingsEnum::SupportEmail->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
])

{{-- CommunityBigLogo --}}
@php $attrName = AppSettingsEnum::CommunityBigLogo->name; @endphp
@include('hhh.widgets.form.upload_photo', [
    'fileAssistant' => $files[$attrName],
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
])

{{-- CommunityMiniLogo --}}
@php $attrName = AppSettingsEnum::CommunityMiniLogo->name; @endphp
@include('hhh.widgets.form.upload_photo', [
    'fileAssistant' => $files[$attrName],
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
])

{{-- CommunityFavicon --}}
@php $attrName = AppSettingsEnum::CommunityFavicon->name; @endphp
@include('hhh.widgets.form.upload_photo', [
    'fileAssistant' => $files[$attrName],
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
])

{{-- IsCommunityDashboradNoteActive --}}
@php $attrName = AppSettingsEnum::IsCommunityDashboradNoteActive->name; @endphp
@php $isCommunityDashboradNoteActive = $setting->$attrName; @endphp
@include('hhh.widgets.form.switch-btn', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'value' => $setting->$attrName,
    'onClick' => sprintf(
        "toggleElementClass('%s-widget','d-none');toggleElementClass('%s-widget','d-none');",
        AppSettingsEnum::CommunityDashboradNoteTitle->name,
        AppSettingsEnum::CommunityDashboradNoteText->name),
])

{{-- CommunityDashboradNoteTitle --}}
@php $attrName = AppSettingsEnum::CommunityDashboradNoteTitle->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'hideWidget' => !$isCommunityDashboradNoteActive,
])

{{-- CommunityDashboradNoteText --}}
@php $attrName = AppSettingsEnum::CommunityDashboradNoteText->name; @endphp
@include('hhh.widgets.form.input-text_area-field', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
    'rows' => 10,
    'style' => 'resize: vertical;',
    'hideWidget' => !$isCommunityDashboradNoteActive,
])

{{-- Form END --}}
@include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.form-end')
