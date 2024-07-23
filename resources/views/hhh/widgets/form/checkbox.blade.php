{{--
    @param $attrName
    @param $label
    @param $value ?? '' : true | false
    @param $disabled ?? '': true | false -optinal- default: false
    @param $hideWidget ?? '': true | false -optinal- default: false
--}}

{{-- Example: --}}

{{--

@php $attrName = "ch_wd_mon"; @endphp
@include('hhh.widgets.form.checkbox',
[
'attrName' => $attrName,
'label' => trans('general.WeekDays.fullName.mon'),
'value' => $ch_wd->mon,
])

--}}

<div id="{{ $attrName }}-widget" class="form-check  form-check-inline form-check-primary @if($hideWidget ?? '') d-none @endif">
    <label class="tab-item-label form-check-label" @if ($disabled ?? '') style="opacity: 0.5;" @endif>
        <input type="checkbox" class="form-check-input"
            name="{{ $attrName }}"
            @if ($value ?? '') value='{{ $value ?? '' }}' @endif
            @if ($checked) checked @endif
            {{-- @if ($disabled ?? '') disabled @endif --}} {{-- Replaced because desabled value not sending via post --}}
            @if ($disabled ?? '') onclick="event.preventDefault();" @endif
             >
        {{ $label }}
    </label>
</div>
