@include('hhh.BackOffice.pages.UserProfile.edit.form-start', ['tabpanel' => 'Personal'])

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa fa-passport"></i></span>
    <div class="media-body">
        <h4 class="mt-0">@lang('PagesContent_PersonnelProfile.tab.Personal.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_PersonnelProfile.tab.Personal.descriptionText')</p>
    </div>
</div>



{{-- Email --}}
@php $attrName = $UsersTableEnum::Email->dbName(); @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.placeholder'),
    'value' => $userProfile->$attrName,
    'style' => 'direction:ltr;',
])

{{-- first_name --}}
@php $attrName = $PersonnelExtrasTableEnum::FirstName->dbName(); @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.placeholder'),
    'value' => $userProfileExtra->$attrName,
])


{{-- last_name --}}
@php $attrName = $PersonnelExtrasTableEnum::LastName->dbName(); @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.placeholder'),
    'value' => $userProfileExtra->$attrName,
])

{{-- alias_name --}}
@php $attrName = $PersonnelExtrasTableEnum::AliasName->dbName(); @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.placeholder'),
    'value' => $userProfileExtra->$attrName,
])

{{-- gender --}}
@php $attrName = $PersonnelExtrasTableEnum::Gender->dbName(); @endphp
@include('hhh.widgets.form.dropdown', [
    'attrName' => $attrName,
    'label' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.notice'),
    'collection' => $GenderCollection,
    'selectedItem' => $userProfileExtra->$attrName,
])

@include('hhh.BackOffice.pages.UserProfile.edit.form-end')
