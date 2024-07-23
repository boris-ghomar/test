@include('hhh.BackOffice.pages.UserProfile.edit.form-start', ['tabpanel' => 'Password'])

{{-- media --}}
<div class="media pb-4">
    <span class="mx-3 h1"><i class="fa fa-user-lock"></i></span>
    <div class="media-body">
        <h4 class="mt-0">@lang('PagesContent_PersonnelProfile.tab.Password.descriptionTitle')</h4>
        <p class="text-justify">@lang('PagesContent_PersonnelProfile.tab.Password.descriptionText')</p>
    </div>
</div>


{{-- current_password --}}
@php $attrName = "current_password";@endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'password',
    'attrName' => $attrName,
    'label' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.placeholder'),
    'value' => old($attrName),
    'style' => 'direction:ltr;',
    'icon' => 'fa fa-lock',
])

{{-- new_password --}}
@php $attrName = "new_password"; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'password',
    'attrName' => $attrName,
    'label' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.placeholder'),
    'value' => old($attrName),
    'style' => 'direction:ltr;',
    'icon' => 'fa fa-lock',
])


{{-- new_password_confirmation --}}
@php $attrName = "new_password_confirmation"; @endphp
@include('hhh.widgets.form.input-field', [
    'type' => 'password',
    'attrName' => $attrName,
    'label' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_PersonnelProfile.form.' . $attrName . '.placeholder'),
    'value' => old($attrName),
    'style' => 'direction:ltr;',
    'icon' => 'fa fa-lock',
])

@include('hhh.BackOffice.pages.UserProfile.edit.form-end')
