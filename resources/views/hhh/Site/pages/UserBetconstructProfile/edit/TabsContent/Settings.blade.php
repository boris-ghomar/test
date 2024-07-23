@include('hhh.Site.pages.UserBetconstructProfile.edit.form-start', ['tabpanel' => 'Settings'])

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa-solid fa-gear"></i></i></span>
    <div class="media-body">
        <h4 class="mt-0">@lang('PagesContent_UserBetconstructProfile.tab.Settings.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_UserBetconstructProfile.tab.Settings.descriptionText')</p>
    </div>
</div>

{{-- CommunityTimeZone --}}
@if ($canClientChangeTimeZone)
    @php $attrName = AppSettingsEnum::CommunityTimeZone->name; @endphp
    @include('hhh.widgets.form.input-field', [
        'type' => 'text',
        'attrName' => $attrName,
        'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
        'notice' => sprintf(
            '%s<br>%s: %s ',
            trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice'),
            trans('general.SystemDefault'),
            $defaults->$attrName),
        'placeholder' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.placeholder'),
        'value' => $setting->$attrName,
        'style' => 'direction:ltr;',
    ])
@endif


{{-- CommunityCalendarType --}}
@if ($canClientChangeCalendarType)
    @php $attrName = AppSettingsEnum::CommunityCalendarType->name; @endphp
    @include('hhh.widgets.form.dropdown', [
        'attrName' => $attrName,
        'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
        'notice' => sprintf(
            '%s<br>%s: %s',
            trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice'),
            trans('general.SystemDefault'),
            $defaults->$attrName),
        'placeholder' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.placeholder'),
        'collection' => $calendarTypeDropdown,
        'selectedItem' => $setting->$attrName,
    ])
@endif

@include('hhh.Site.pages.UserBetconstructProfile.edit.form-end')
