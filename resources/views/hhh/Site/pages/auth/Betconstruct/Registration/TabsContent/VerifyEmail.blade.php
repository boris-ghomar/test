@include('hhh.Site.pages.auth.Betconstruct.Registration.form-start', ['tabpanel' => 'VerifyEmail'])

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa-solid fa-envelope-circle-check"></i></span>
    <div class="media-body">
        <h4 class="mt-0">@lang('PagesContent_RegisaterationBetconstruct.tab.VerifyEmail.descriptionTitle')</h4>
        <p class="text-justify">
            @lang('PagesContent_RegisaterationBetconstruct.tab.VerifyEmail.descriptionText', ['email' => $underVerifyEmail])
        </p>
    </div>
</div>

{{-- EmailVerificationCode --}}
@php $attrName = "EmailVerificationCode"; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'number',
    'attrName' => $attrName,
    'label' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_RegisaterationBetconstruct.form.' . $attrName . '.placeholder'),
    'value' => null,
    'style' => 'direction: ltr;',
])
<div class="pb-3 text-warning">
    {{ $nextEmailVerificationMsg }}
</div>

@include('hhh.Site.pages.auth.Betconstruct.Registration.form-end')
