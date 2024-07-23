@include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.form-start')

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa fa-message-bot"></i></span>
    <div class="media-body">
        <h4 class="tab-item-label mt-0">@lang('PagesContent_GeneralSettings.tab.Chatbot.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_GeneralSettings.tab.Chatbot.descriptionText')</p>
    </div>
</div>


{{-- ChatbotProfileImage --}}
@php $attrName = AppSettingsEnum::ChatbotProfileImage->name; @endphp
@include('hhh.widgets.form.upload_photo', [
    'fileAssistant' => $files[$attrName],
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
])

{{-- ChatbotInactiveChatsExpirationHours --}}
@php $attrName = AppSettingsEnum::ChatbotInactiveChatsExpirationHours->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'number',
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
])

{{-- ChatbotClosedChatsDaysOfKeeping --}}
@php $attrName = AppSettingsEnum::ChatbotClosedChatsDaysOfKeeping->name; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'number',
    'attrName' => $attrName,
    'label' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_GeneralSettings.form.' . $attrName . '.placeholder'),
    'value' => $setting->$attrName,
])


{{-- Form END --}}
@include('hhh.BackOffice.pages.Settings.GeneralSettings.edit.form-end')
