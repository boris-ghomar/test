{{--
    @param $attrName
    @param $label
    @param $notice
    @param $collection ($collection as $itemValue => $itemLabel) Example: ['sat' => 'Saturday', 'sun' => 'Sunday', 'mon' => 'Monday']
    @param $selectedItemsList Array of selected values : Example: $selectedItemsList = ['sun','sat'] | null | []
    @param $hideWidget ?? '': true | false -optinal- default: false
    @param $disabled ?? '': true | false -optinal- default: false // used in child elements: "hhh.widgets.form.checkbox"
    @param $collapse ?? '': true | false -optinal- default: false; true ? collapse : display each item in separate line
    @param $useSelectButtons ?? '': true | false -optinal- if true ? the buttons for select|unselect all will be attached

    @return array of selected items
        Example : $request->$attrName = ['sun','sat']
--}}

{{-- Example: --}}

{{--

@php $attrName = "trading_days"; @endphp
@include('hhh.widgets.form.checkbox-group',
[
'attrName' => $attrName,
'label' => trans('PagesContent_OfficeGeneralSettings.form.' . $attrName .'.name' ),
'notice' => trans('PagesContent_OfficeGeneralSettings.form.' . $attrName .'.notice' ),
'collection' => $tradingDaysCollection,
'selectedItemsList' => json_decode($office_setting->$attrName),
])

--}}
@php
    if (is_null($collection)) {
        $collection = [];
    }

    if (!empty($collection) && array_is_list($collection)) {
        // Convert list to key => value format

        $newCollection = [];

        foreach ($collection as $item) {
            $newCollection[$item] = $item;
        }

        $collection = $newCollection;
    }

    if (is_null($selectedItemsList)) {
        $selectedItemsList = [];
    }

@endphp

<div id="{{ $attrName }}-widget" class="form-group @if ($hideWidget ?? '') d-none @endif">
    <label for="{{ $attrName }}" class="tab-item-label my-auto font-weight-bold">
        {{ $label }}
    </label>
    @if (!empty($notice))
        <label class="d-block col-sm-12 p-0"><span class='text-gray'>{!! $notice !!}</span></label>
    @endif

    {{-- This returns the null value for the array variable when no item is selected --}}
    <input type="hidden" name="{{ $attrName }}">

    <div class="form-inline blockquote">

        <div @if ($collapse ?? '') style="display: flex;flex-direction: row;flex-wrap: wrap;" @endif>
            @foreach ($collection as $itemValue => $itemLabel)
                @include('hhh.widgets.form.checkbox', [
                    'attrName' => $attrName . '[]',
                    'label' => $itemLabel,
                    'value' => $itemValue,
                    'checked' => in_array($itemValue, $selectedItemsList),
                    'hideWidget' => false,
                ])
            @endforeach
        </div>

        @if ($useSelectButtons ?? '')
            @if (!empty($collection) && count($collection) > 1)
                @php
                    $selectAllBtnAction = sprintf("document.querySelectorAll('input[name=\'%s[]\']:not(:checked)').forEach(el => el.checked = true);", $attrName);
                    $unselectAllBtnAction = sprintf("document.querySelectorAll('input[name=\'%s[]\']:checked').forEach(el => el.checked = false);", $attrName);
                @endphp

                <div class="d-block">
                    <button id="" type="button" onclick="{{ $selectAllBtnAction }}"
                        class="btn btn-icon-text btn-inverse-primary btn-fw" title="@lang('general.buttons.SelectAll')">
                        <i class="fa-solid fa-square-check btn-icon-prepend"></i>@lang('general.buttons.SelectAll')
                    </button>

                    <button id="" type="button" onclick="{{ $unselectAllBtnAction }}"
                        class="btn btn-icon-text btn-inverse-primary btn-fw" title="@lang('general.buttons.UnselectAll')">
                        <i class="fa-regular fa-square btn-icon-prepend"></i>@lang('general.buttons.UnselectAll')
                    </button>
                </div>
            @endif
        @endif

        @include('hhh.widgets.messages.ShowFieldAllErrors')
    </div>

</div>
