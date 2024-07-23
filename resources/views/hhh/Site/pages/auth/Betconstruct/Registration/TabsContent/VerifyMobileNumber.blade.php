@include('hhh.Site.pages.auth.Betconstruct.Registration.form-start', ['tabpanel' => 'VerifyMobileNumber'])

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa-solid fa-mobile-signal-out"></i></span>
    <div class="media-body">
        <h4 class="mt-0">@lang('PagesContent_RegisaterationBetconstruct.tab.VerifyMobileNumber.descriptionTitle')</h4>
        <p class="text-justify">
            @lang('PagesContent_RegisaterationBetconstruct.tab.VerifyMobileNumber.descriptionText', ['mobileNumber' => $underVerifyMobileNumber])
        </p>
    </div>
</div>

{{-- MobileVerificationCode --}}
@php $attrName = "MobileVerificationCode"; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'number',
    'attrName' => $attrName,
    'label' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.placeholder'),
    'value' => null,
    'hideArrows' => true,
    'style' => 'direction: ltr;',
])
<div class="pb-3 text-warning">
    {{ $nextMobileVerificationMsg }}
</div>

@include('hhh.Site.pages.auth.Betconstruct.Registration.form-end')
