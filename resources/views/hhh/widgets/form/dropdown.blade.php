{{--
    @param $attrName
    @param $label
    @param $notice
    @param $prependCollection (as $text => $value) Example : ['' => -1]
    @param $collection (as $text => $value) Example : ['text1' => 'value1','text2' => 'value2']
    @param $selectedItem : selected item $value
    @param $disabled ?? '': true | false -optinal- default: false
    @param $hideWidget ?? '': true | false -optinal- default: false
--}}

{{-- Example: --}}

{{--

@php $attrName = "client_default_country"; @endphp
@include('hhh.widgets.form.dropdown',
[
'attrName' => $attrName,
'label' => trans('PagesContent_OfficeGeneralSettings.form.' . $attrName .'.name' ),
'notice' => trans('PagesContent_OfficeGeneralSettings.form.' . $attrName .'.notice' ),
'collection' => $countriesCollection,
'selectedItem' => $office_setting->$attrName,
])


--}}

<div id="{{ $attrName }}-widget" class="form-group @if ($hideWidget ?? '') d-none @endif">
    <label for="{{ $attrName }}" class="tab-item-label my-auto font-weight-bold">
        {{ $label }}
    </label>
    @if (!empty($notice))
        <label class="d-block col-sm-12 p-0"><span class='text-gray'>{!! $notice !!}</span></label>
    @endif
    <select class="form-control @error($attrName) is-invalid @enderror" id="{{ $attrName }}" name="{{ $attrName }}"
        @if ($disabled ?? '') disabled @endif >

        {{-- The replacement of key and text is done so that when dealing with numerical Enums, the assignment of the value does not interfere with the index of the array. --}}
        @if ($prependCollection ?? '')
            @foreach ($prependCollection as $text => $value)
                <option value='{{ $value }}' @if ($selectedItem == $value) selected @endif>{{ $text }}
                </option>
            @endforeach
        @endif

        @foreach ($collection as $text => $value)
            <option value='{{ $value }}' @if ($selectedItem == $value) selected @endif>{{ $text }}
            </option>
        @endforeach
    </select>
    @include('hhh.widgets.messages.ShowFieldAllErrors')

</div>
<input type="hidden" id="{{ $attrName }}_selectedItem" value="{{ ($selectedItem) }}">
