{{--
    @param $attrName
    @param $label
    @param $notice
    @param $value

    -optinal-
    @param $style ?? '' -optinal-
    @param $disabled ?? '': true | false -optinal- default: false
    @param $hideWidget ?? '': true | false -optinal- default: false
--}}

{{-- Example: --}}

{{--

    Example 1:

    @php $attrName = $ClientExtrasTableEnum::BirthDateStamp->dbName(); @endphp
    @include('hhh.widgets.form.date-input-field', [
    'type' => 'text',
    'attrName' => $attrName,
    'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice',['calendarType' => $userProfile->getCalendarType()->translate()]),
    'placeholder' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.placeholder'),
    'value' => $userProfileExtra->$attrName,
])



--}}

@php
    // Split date

    $year = '';
    $month = '';
    $day = '';
    $separator = '-';

    if (!empty($value)) {
        if (Str::of($value)->contains('/')) {
            $separator = '/';
        } elseif (Str::of($value)->contains('-')) {
            $separator = '-';
        }

        try {
            [$year, $month, $day] = explode($separator, $value);
        } catch (\Throwable $th) {
        }
    }
@endphp
<div id="{{ $attrName }}-widget" class="form-group @if ($hideWidget ?? '') d-none @endif">
    <label class="tab-item-label my-auto font-weight-bold" for="{{ $attrName }}">
        {{ $label }}
    </label>
    @if (!empty($notice))
        <label class="d-block col-sm-12 p-0"><span class='text-gray'>{!! $notice !!}</span></label>
    @endif

    <div class="input-group d-flex justify-content-around @error($attrName) is-invalid @enderror">

        <div style="width: 33.33%">
            @php $inputAttrName = $attrName . "_day"; @endphp
            <input type="number" name="{{ $inputAttrName }}"
                class="form-control form-control-lg ltr text-center  @error($inputAttrName) is-invalid @enderror"
                style="{{ $style ?? '' }}" placeholder="@lang('general.timeScops.Day')" value="{{ old($inputAttrName, $day) }}"
                @if ($disabled ?? '') disabled @endif min="1", max="31">
            @include('hhh.widgets.messages.ShowFieldAllErrors', ['attrName' => $inputAttrName])
        </div>

        <div style="width: 33.33%">
            @php $inputAttrName = $attrName . "_month"; @endphp
            <input type="number" name="{{ $inputAttrName }}"
                class="form-control form-control-lg ltr text-center  @error($inputAttrName) is-invalid @enderror"
                style="{{ $style ?? '' }}" placeholder="@lang('general.timeScops.Month')" value="{{ old($inputAttrName, $month) }}"
                @if ($disabled ?? '') disabled @endif min="1", max="12">
            @include('hhh.widgets.messages.ShowFieldAllErrors', ['attrName' => $inputAttrName])
        </div>

        <div style="width: 33.33%">
            @php $inputAttrName = $attrName . "_year"; @endphp
            <input type="number" name="{{ $inputAttrName }}"
                class="form-control form-control-lg ltr text-center  @error($inputAttrName) is-invalid @enderror"
                style="{{ $style ?? '' }}" placeholder="@lang('general.timeScops.Year')" value="{{ old($inputAttrName, $year) }}"
                @if ($disabled ?? '') disabled @endif min="1330">
            @include('hhh.widgets.messages.ShowFieldAllErrors', ['attrName' => $inputAttrName])
        </div>
    </div>

    <input type="hidden" name="{{ $attrName }}" value="{{ $value }}">

    @include('hhh.widgets.messages.ShowFieldAllErrors')

</div>
