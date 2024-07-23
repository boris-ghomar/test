{{--
    @param $attrName
    @param $label
    @param $notice
    @param $placeholder
    @param $value
    @param $rows
    @param $style ?? '' -optinal-
    @param $disabled ?? '': true | false -optinal- default: false
    @param $hideWidget ?? '': true | false -optinal- default: false
--}}

{{-- Example: --}}

{{--

@php $attrName = "other_info"; @endphp
@include('hhh.widgets.form.input-text_area-field',
[
'attrName' => $attrName,
'label' => trans('PagesContent_PersonnelProfile..form.attributes.' . $attrName ),
'notice' => trans('PagesContent_PersonnelProfile..form.notice.' . $attrName ),
'placeholder' => trans('PagesContent_PersonnelProfile..form.placeholder.' . $attrName ),
'value' => $office_profile->$attrName,
'rows' => 10,
]
)

--}}

<div id="{{ $attrName }}-widget" class="form-group @if ($hideWidget ?? '') d-none @endif">
    <label class="tab-item-label my-auto font-weight-bold" for="{{ $attrName }}">
        {{ $label }}
    </label>
    @if (!empty($notice))
        {{-- <label class="col-sm-12 p-0"><span class='text-gray'>{{ $notice }}</span></label> --}}
        <label class="d-block col-sm-12 p-0"><span class='text-gray'>{!! $notice !!}</span></label>
    @endif
    <textarea class="form-control @error($attrName) is-invalid @enderror" id="{{ $attrName }}" name="{{ $attrName }}"
        rows="{{ $rows }}" @if ($disabled ?? '') disabled @endif placeholder="{{ $placeholder }}"
        style="white-space: normal;line-height:normal; {{ $style ?? '' }}">{{ old($attrName, $value) }}</textarea>

    @include('hhh.widgets.messages.ShowFieldAllErrors')
</div>
