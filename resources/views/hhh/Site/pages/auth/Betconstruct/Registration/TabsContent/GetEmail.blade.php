@include('hhh.Site.pages.auth.Betconstruct.Registration.form-start', ['tabpanel' => 'GetEmail'])

@php
    $isEmailVerificationIsRequired = AppSettingsEnum::CommunityRegistrationEmailVerificationIsRequired->getValue();
@endphp
{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa-solid fa-envelope"></i></span>
    <div class="media-body">
        <h4 class="mt-0">@lang('PagesContent_RegisaterationBetconstruct.tab.GetEmail.descriptionTitle')</h4>
        <p class="text-justify">
            @if ($isEmailVerificationIsRequired)
                @lang('PagesContent_RegisaterationBetconstruct.tab.GetEmail.descriptionText.withVerification')
            @else
                @lang('PagesContent_RegisaterationBetconstruct.tab.GetEmail.descriptionText.withoutVerification')
            @endif
        </p>
    </div>
</div>


{{-- email --}}
@php $attrName = $ClientExtrasTableEnum::Email->dbName(); @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.placeholder'),
    'value' => $underVerifyEmail,
    'style' => 'direction: ltr;',
])

@include('hhh.Site.pages.auth.Betconstruct.Registration.form-end')
