{{--
    ---------------------------------------------------------
    @Requirements
        CSS:
            <link rel="stylesheet" type="text/css" href={{ url('widgets/bootstrap_clockpicker/css/bootstrap-clockpicker.min.css') }}>

        javaScript:

            -- time picker --
            <script type="text/javascript" src={{ url('widgets/bootstrap_clockpicker/js/bootstrap-clockpicker.min.js') }}></script>

            <script type="text/javascript">
                $('.clockpicker').clockpicker()
                    .find('input').change(function(){
                    console.log(this.value);
                });
                var input = $('#single-input').clockpicker({
                    placement: 'bottom',
                    align: 'left',
                    autoclose: true,
                    'default': 'now'
                });

            </script>
        -- time picker END --

    ---------------------------------------------------------

    @param $attrName
    @param $label
    @param $notice
    @param $placeholder
    @param $value

    @param $clockAlign : 'left' | 'right' :: Direction of the clock display
    @param $clockPlacement : 'top' | 'bottom' | 'left' | 'right' :: Placement of the clock display based on the input field
    @param $donetext : The text of "Done" button

    -optinal-
    @param $showIcon ?? '' ?? '' : true | false -optinal- default: true
    @param $style ?? '' -optinal-
    @param $disabled ?? '': true | false -optinal- default: false
    @param $hideWidget ?? '': true | false -optinal- default: false
--}}

{{-- Example: --}}

{{--

@php $attrName = "daily_start_time"; @endphp
@include('hhh.widgets.form.bootstrap_clockpicker',
[
'attrName' => $attrName,
'label' => trans('PagesContent_OfficeGeneralSettings.form.' . $attrName .'.name' ),
'notice' => trans('PagesContent_OfficeGeneralSettings.form.' . $attrName .'.notice' ),
'placeholder' => trans('PagesContent_OfficeGeneralSettings.form.' . $attrName .'.placeholder' ),
'value' => $office_setting->trading_hours[$attrName],
'clockAlign' => trans('general.locale.start'),
'clockPlacement' => "top",
'donetext' => trans('general.Done'),
]
)

--}}

<div id="{{ $attrName }}-widget" class="form-group @if ($hideWidget ?? '') d-none @endif">
    <label class="tab-item-label my-auto font-weight-bold">{{ $label }}</label>
    @if (!empty($notice))
        <label class="d-block col-sm-12 p-0"><span class='text-gray'>{!! $notice !!}</span></label>
    @endif
    <div class="input-group clockpicker" data-placement="{{ $clockPlacement }}" data-align="{{ $clockAlign }}"
        data-donetext="{{ $donetext }}">

        <input type="text" class="form-control font-weight-bold" id="{{ $attrName }}" name="{{ $attrName }}"
            placeholder="{{ $placeholder }}" value="{{ old($attrName, $value) }}"
            @if ($disabled ?? '') disabled @endif style="{{ $style ?? '' }}" readonly>

        {{-- <div class="input-group-addon"></div> --}}
        @if ($showIcon ?? '' !== false)
            <span class="input-group-addon input-group-append border-left">
                <span class="fa fa-clock input-group-text"></span>
            </span>
        @endif

    </div>

</div>
