{{-- @include('hhh.Site.pages.UserBetconstructProfile.edit.form-start', ['tabpanel' => 'Account']) --}}

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa fa-passport"></i></span>
    <div class="media-body">
        <h4 class="mt-0">@lang('PagesContent_UserBetconstructProfile.tab.Account.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_UserBetconstructProfile.tab.Account.descriptionText')</p>
    </div>
</div>



{{-- id --}}
@php $attrName = $ClientExtrasTableEnum::Id->dbName(); @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.placeholder'),
    'value' => $userProfileExtra->$attrName,
    'disabled' => true,
])

{{-- login --}}
@php $attrName = $ClientExtrasTableEnum::Login->dbName(); @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.placeholder'),
    'value' => $userProfileExtra->$attrName,
    'disabled' => true,
])

{{-- first_name --}}
@php $attrName = $ClientExtrasTableEnum::FirstName->dbName(); @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.placeholder'),
    'value' => $userProfileExtra->$attrName,
    'disabled' => true,
])

{{-- last_name --}}
@php $attrName = $ClientExtrasTableEnum ::LastName->dbName(); @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.placeholder'),
    'value' => $userProfileExtra->$attrName,
    'disabled' => true,
])

{{-- phone --}}
@php $attrName = $ClientExtrasTableEnum::Phone->dbName(); @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.placeholder'),
    'value' => $userProfileExtra->$attrName,
    'disabled' => true,
])

{{-- mobile_phone --}}
{{-- Disabled: Betconstruct is using the phone number as mobile number --}}
{{-- @php $attrName = $ClientExtrasTableEnum::MobilePhone->dbName(); @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.placeholder'),
    'value' => $userProfileExtra->$attrName,
    'disabled' => true,
]) --}}

{{-- created_stamp --}}
@php $attrName = $ClientExtrasTableEnum::CreatedStamp->dbName(); @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.placeholder'),
    'value' => $userProfileExtra->$attrName,
    'disabled' => true,
])

{{-- @include('hhh.Site.pages.UserBetconstructProfile.edit.form-end') --}}
