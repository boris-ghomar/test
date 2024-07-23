{{--
    @param $type : 'text' | 'number' | ...
    @param $attrName
    @param $label
    @param $notice
    @param $placeholder
    @param $value

    -optinal-
    @param $style ?? '' -optinal-
    @param $icon ?? '' -optinal- : You can use font-icons  : example: 'icon' => 'fa fa-lock',
    @param $disabled ?? '': true | false -optinal- default: false
    @param $step ?? '': For numeric inputs -optinal- default: 1, Example: $step=0.1
    @param $min ?? '': For numeric inputs -optinal- Example: $min=1
    @param $max ?? '': For numeric inputs -optinal- Example: $max=10
    @param $hideWidget ?? '': true | false -optinal- default: false
    @param $hideArrows ?? '': true | false -optinal- default: false for number type fields
--}}

{{-- Example: --}}

{{--

    Example 1:
-- client_default_leverage --

@php $attrName = "client_default_leverage"; @endphp
@include('hhh.widgets.form.input-field',
[
'type' => 'number',
'attrName' => $attrName,
'label' => trans('PagesContent_OfficeGeneralSettings.form.' . $attrName .'.name' ),
'notice' => trans('PagesContent_OfficeGeneralSettings.form.' . $attrName .'.notice' ),
'placeholder' => trans('PagesContent_OfficeGeneralSettings.form.' . $attrName .'.placeholder' ),
'value' => $office_setting->$attrName,
'step' => 1,
'min' => config('hhh_config.validation.min.leverage'),
'max' => config('hhh_config.validation.max.leverage'),
'style' => 'direction:ltr;',
]
)

Example 2:
-- email --
@php $attrName = "email"; @endphp
@include('hhh.widgets.form.input-field',
[
'type' => 'text',
'attrName' => $attrName,
'label' => trans('PagesContent_PersonnelProfile.form.' . $attrName .'.name' ),
'notice' => trans('PagesContent_PersonnelProfile.form.' . $attrName .'.notice' ),
'placeholder' => trans('PagesContent_PersonnelProfile.form.' . $attrName .'.placeholder' ),
'value' => $user_profile->$attrName,
'style' => 'direction:ltr;',
'icon' => 'fa fa-envelope',
]
)


--}}
<div id="{{ $attrName }}-widget" class="form-group @if ($hideWidget ?? '') d-none @endif">
    <label class="tab-item-label my-auto font-weight-bold" for="{{ $attrName }}">
        {{ $label }}
    </label>
    @if (!empty($notice))
        <label class="d-block col-sm-12 p-0"><span class='text-gray'>{!! $notice !!}</span></label>
    @endif
    <div class="input-group @error($attrName) is-invalid @enderror">

        @if ($icon ?? '')
            <div class="input-group-prepend bg-transparent">
                <span class="input-group-text bg-transparent border-0">
                    <i class="{{ $icon }} text-primary"></i>
                </span>
            </div>
        @endif

        <input type="{{ $type }}" class="form-control form-control-lg border-0 @if ($hideArrows ?? '') no-arrows @endif " id="{{ $attrName }}"
            name="{{ $attrName }}" style="{{ $style ?? '' }}" placeholder="{{ $placeholder }}"
            value="{{ old($attrName, $value) }}" @if ($disabled ?? '') disabled @endif
            @if ($type === 'number') @if ($step ?? '') step="{{ $step }}" @endif
            @if ($min ?? '') min="{{ $min }}" @endif
            @if ($max ?? '') max="{{ $max }}" @endif @endif
        >
    </div>
    @include('hhh.widgets.messages.ShowFieldAllErrors')

</div>
