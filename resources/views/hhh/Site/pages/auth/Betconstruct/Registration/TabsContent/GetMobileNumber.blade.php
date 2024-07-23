@include('hhh.Site.pages.auth.Betconstruct.Registration.form-start', ['tabpanel' => 'GetMobileNumber'])

@php
    $isMobileVerificationIsRequired = AppSettingsEnum::CommunityRegistrationMobileVerificationIsRequired->getValue();
@endphp
{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa-solid fa-mobile-screen-button"></i></span>
    <div class="media-body">
        <h4 class="mt-0">@lang('PagesContent_RegisaterationBetconstruct.tab.GetMobileNumber.descriptionTitle')</h4>
        <p class="text-justify">
            @if ($isMobileVerificationIsRequired)
                @lang('PagesContent_RegisaterationBetconstruct.tab.GetMobileNumber.descriptionText.withVerification')
            @else
                @lang('PagesContent_RegisaterationBetconstruct.tab.GetMobileNumber.descriptionText.withoutVerification')
            @endif
        </p>
    </div>
</div>


{{-- mobile_phone --}}
@php $attrName = $ClientExtrasTableEnum::MobilePhone->dbName(); @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'number',
    'attrName' => $attrName,
    'label' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.placeholder'),
    'value' => $underVerifyMobileNumber,
    'hideArrows' => true,
    'style' => 'direction: ltr;',
])

@include('hhh.Site.pages.auth.Betconstruct.Registration.form-end')
