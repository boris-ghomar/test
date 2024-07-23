@include('hhh.BackOffice.pages.UserProfile.edit.form-start', ['tabpanel' => 'Settings'])

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa-solid fa-gear"></i></i></span>
    <div class="media-body">
        <h4 class="mt-0">@lang('PagesContent_PersonnelProfile.tab.Settings.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_PersonnelProfile.tab.Settings.descriptionText')</p>
    </div>
</div>

{{-- AdminPanelTimeZone --}}
@if ($canPersonnelChangeTimeZone)
    @php $attrName = AppSettingsEnum::AdminPanelTimeZone->name; @endphp
    @include('hhh.widgets.form.input-field', [
        'type' => 'text',
        'attrName' => $attrName,
        'label' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.name'),
        'notice' => sprintf(
            '%s<br>%s: %s ',
            trans('PagesContent_PersonnelProfile.form.' . $attrName . '.notice'),
            trans('general.SystemDefault'),
            $defaults->$attrName),
        'placeholder' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.placeholder'),
        'value' => $setting->$attrName,
        'style' => 'direction:ltr;',
    ])
@endif


{{-- AdminPanelCalendarType --}}
@if ($canPersonnelChangeCalendarType)
    @php $attrName = AppSettingsEnum::AdminPanelCalendarType->name; @endphp
    @include('hhh.widgets.form.dropdown', [
        'attrName' => $attrName,
        'label' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.name'),
        'notice' => sprintf(
            '%s<br>%s: %s',
            trans('PagesContent_PersonnelProfile.form.' . $attrName . '.notice'),
            trans('general.SystemDefault'),
            $defaults->$attrName),
        'placeholder' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.placeholder'),
        'collection' => $calendarTypeDropdown,
        'selectedItem' => $setting->$attrName,
    ])
@endif

@include('hhh.BackOffice.pages.UserProfile.edit.form-end')
