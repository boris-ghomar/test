{{--
    @Source:
        https://bootstrap-datepicker.readthedocs.io/en/latest/
    ---------------------------------------------------------
    @Requirements
        CSS:
            <link rel="stylesheet" href="{{ url('back_office/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">

        javaScript:
            <script src="{{ url('back_office/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

    ---------------------------------------------------------
    @param $attrName
    @param $label
    @param $notice
    @param $placeholder
    @param $value

    -optinal-
    @param $showIcon ?? '' ?? '' : true | false -optinal- default: true
    @param $style ?? '' -optinal-
    @param $disabled ?? '': true | false -optinal- default: false
    @param $hideWidget ?? '': true | false -optinal- default: false
--}}

{{-- Example: --}}

{{--

@php $attrName = "birthday"; @endphp
@include('hhh.widgets.form.bootstrap_datepicker',
[
'attrName' => $attrName,
'label' => trans('PagesContent_PersonnelProfile.form.' . $attrName .'.name' ),
'notice' => trans('PagesContent_PersonnelProfile.form.' . $attrName .'.notice' ),
'placeholder' => trans('PagesContent_PersonnelProfile.form.' . $attrName .'.placeholder' ),
'value' => $user_profile->$attrName->toDateString(),
]
)

--}}

<div id="{{ $attrName }}-widget" class="form-group @if ($hideWidget ?? '') d-none @endif">
    <label class="tab-item-label my-auto font-weight-bold">{{ $label }}</label>
    @if (!empty($notice))
        <label class="d-block col-sm-12 p-0"><span class='text-gray'>{!! $notice !!}</span></label>
    @endif
    <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">

        <input type="text" class="form-control font-weight-bold" id="{{ $attrName }}" name="{{ $attrName }}"
            placeholder="{{ $placeholder }}" value="{{ old($attrName, $value) }}"
            @if ($disabled ?? '') disabled @endif
            @if ($style ?? '') style="{{ $style ?? '' }}" @endif readonly>

        <div class="input-group-addon"></div>

        @if ($showIcon ?? '' !== false)
            <span class="input-group-addon input-group-append border-left">
                <span class="fa fa-calendar-alt input-group-text"></span>
            </span>
        @endif
    </div>

</div>
