{{--
    @param $attrName
    @param $label
    @param $notice
    @param $value : true | false
    @param $disabled ?? '': true | false -optinal- default: false
    @param $onClick ?? ''-optinal-
    @param $hideWidget ?? '': true | false -optinal- default: false
--}}

{{-- Example: --}}

{{--

@php $attrName = "is_tarde_active"; @endphp
@include('hhh.widgets.form.switch-btn',
[
'attrName' => $attrName,
'label' => trans('PagesContent_OfficeGeneralSettings.form.' . $attrName .'.name' ),
'notice' => trans('PagesContent_OfficeGeneralSettings.form.' . $attrName .'.notice' ),
'value' => $office_setting->$attrName,
])

--}}
<div id="{{ $attrName }}-widget" class="form-group row m-0 mb-3 @if ($hideWidget ?? '') d-none @endif">
    <div class="custom-control custom-switch d-inline">
        <input type="checkbox" class="custom-control-input" id="{{ $attrName }}"
            @if ($value) checked @endif
            @if ($disabled ?? '') disabled @else onchange="$(this).next().val(this.checked?1:0);$(this).val(this.checked?1:0)" @endif
            @if ($onClick ?? '') onclick="{{ $onClick }}" @endif value="{{ $value ? '1' : '0' }}">
        <input type="hidden" name="{{ $attrName }}" value="{{ $value ? '1' : '0' }}">


        <label class="tab-item-label custom-control-label font-weight-bold ms-1"
            for="{{ $attrName }}">{{ $label }}</label>
    </div>

    @if (!empty($notice))
        <label class="d-block col-sm-12"><span class='text-gray'>{!! $notice !!}</span></label>
    @endif
</div>
