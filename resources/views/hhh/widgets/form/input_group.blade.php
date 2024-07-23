{{--
    @param $attrName
    @param $type : 'text' | 'number' | ...
    @param $label
    @param $notice
    @param $collection ($collection as array list) Example: ['item1','item2','item3']
    @param $hideWidget ?? '': true | false -optinal- default: false
    @param $hideArrows ?? '': true | false -optinal- default: false for number type fields
    @param $filedStyle ?? '': -optinal-

    @return array of selected items
        Example : $request->$attrName = ['item1','item2']
--}}

{{-- Example: --}}

{{--

@php $attrName = $ClientExtrasTableEnum::ContactNumbersInternal->dbName(); @endphp
@include('hhh.widgets.form.input_group', [
    'attrName' => $attrName,
    'type' => 'number',
    'label' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.name'),
    'notice' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.notice'),
    'placeholder' => trans('PagesContent_UserBetconstructProfile.form.' . $attrName . '.placeholder'),
    'collection' => $userProfileExtra->$attrName,
    'filedStyle' => 'direction: rtl;',
    'jsController' => 'contactNumbersInternalCtl',
])

--}}

<div id="{{ $attrName }}-widget" class="form-group @if ($hideWidget ?? '') d-none @endif">
    <label class="tab-item-label my-auto font-weight-bold" for="{{ $attrName }}">
        {{ $label }}
    </label>
    <label class="col-sm-12 p-0 @if (empty($notice)) d-none @endif"><span
            class="text-gray">{{ $notice }}</span></label>
    <div id="{{ $attrName }}_DisplaySection"></div>
    <button id="{{ $attrName }}_addNewFieldBtn" type="button" class="btn btn-success btn-rounded"
        style="height: 35px; width:35px;padding:0;" onclick="{{ $jsController }}.addNewField();"><i
            class="fa fa-solid fa-plus"></i></button>

    {{-- Used for javascript first load --}}
    <input type="hidden" id="Existing_{{ $attrName }}" value="{{ json_encode($collection) }}">
    <div class="mt-2">
        @include('hhh.widgets.messages.ShowFieldAllErrors')
    </div>

</div>

{{-- attribute field Template --}}
<div id= "{{ $attrName }}_NewFiledTemplate" class="d-none">
    <div id="filedInputSection_TemplateID_{{ $attrName }}">

        <div class="d-flex justify-content-between align-items-center">
            <div style="width: 100%;">
                <div name="{{ $attrName }}_Filed" class="input-group mb-2">
                    <input id="filedInput_TemplateID_{{ $attrName }}" type="{{ $type }}"
                        class="form-control form-control-lg border-0 @if ($type == 'number') ltr text-left @endif @if ($hideArrows ?? '') no-arrows @endif "
                        @if ($filedStyle ?? '') style="{{ $filedStyle }}" @endif
                        placeholder="{{ $placeholder }}" oninput="this.setAttribute('value', this.value );">
                </div>
            </div>
            <div>
                <a type="button" class="text-danger px-3"
                    onclick="{{ $jsController }}.deleteField('filedInputSection_TemplateID_{{ $attrName }}');"><i
                        class="fa-solid fa-trash-can"></i></a>
            </div>
        </div>
    </div>

</div>

{{-- Fields errors --}}
{{--
    $filedsErrors = [
                        "contact_numbers_internal.0" => array:1 [
                            0 => "تعداد ارقام مورد 'شماره تماس' باید بین 8 و 11 رقم باشد."
                        ],
                        "contact_numbers_internal.1" => array:1 [
                            0 => "تعداد ارقام مورد 'شماره تماس' باید بین 8 و 11 رقم باشد."
                        ],
                    ]
    --}}

<div class="d-none">
    @php
        $filedsErrors = $errors->get($attrName . '.*');
    @endphp

    @foreach ($filedsErrors as $fieldName => $fieldErrors)
        <div id="{{ $fieldName }}">
            @include('hhh.widgets.messages.ShowFieldAllErrors', ['attrName' => $fieldName])
        </div>
    @endforeach
</div>
