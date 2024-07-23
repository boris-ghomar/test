@php
    $VerificationStatusEnum = App\Enums\Users\VerificationStatusEnum::class;

    $isUnderVerify = $emailVerificationStatus == $VerificationStatusEnum::UnderVerify->name;
    $canSaveForm = $emailVerificationStatus != $VerificationStatusEnum::NeedVerify->name;
@endphp

@if ($canSaveForm)
    @include('hhh.Site.pages.UserBetconstructProfile.edit.form-start', ['tabpanel' => 'ChangeEmail'])
@endif

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa-solid fa-envelope"></i></span>
    <div class="media-body">
        <h4 class="mt-0">@lang('PagesContent_UserBetconstructProfile.tab.ChangeEmail.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_UserBetconstructProfile.tab.ChangeEmail.descriptionText')</p>

    </div>
</div>

{{-- Email --}}
@php $attrName = $ClientExtrasTableEnum::Email->dbName(); @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.placeholder'),
    'value' => old($attrName, $userProfileExtra->$attrName),
    'style' => 'direction:ltr;',
    'disabled' => $isUnderVerify,
])

@if ($emailVerificationStatus == $VerificationStatusEnum::NeedVerify->name)
    <div class="mb-4">
        <div class="text-danger"><i class="fa-solid fa-circle-info"></i> @lang('thisApp.unverifiedEmail')</div>
        <a type="button" class="btn btn-success ms-3 mt-3"
            onclick="clientProfileCtl.sendEmailVerification();">@lang('general.buttons.SendVerificationEmail')</a>
    </div>
@endif

{{-- emailVerificationCode --}}
@if ($isUnderVerify)
    @php $attrName = "emailVerificationCode" @endphp
    @include('hhh.widgets.form.input-field', [
        'type' => 'number',
        'attrName' => $attrName,
        'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
        'notice' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice'),
        'placeholder' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.placeholder'),
        'value' => '',
        'style' => 'direction:ltr;',
    ])

    <div class="pb-3 text-warning">
        {{ $nextVerificationEmailMsg }}
    </div>
@endif

@if ($canSaveForm)
    @include('hhh.Site.pages.UserBetconstructProfile.edit.form-end')
@endif
