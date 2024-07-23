{{--
    @param $attrName
    @param $label
    @param $notice
    @param $collection ($collection as $itemValue => $itemLabel) Example: ['sat' => 'Saturday', 'sun' => 'Sunday', 'mon' => 'Monday']
    @param $selectedItem
    @param $hideWidget ?? '': true | false -optinal- default: false

    @return array of selected item
--}}

{{-- Example: --}}

{{--

 @php $attrName = "RecoveryMethod" @endphp
 @include('hhh.widgets.form.radio_group', [
       'attrName' => $attrName,
        'label' => trans(
        'auth_site.custom.ForgotPasswordForm.index.' . $attrName . '.name'),
        'notice' => trans(
        'auth_site.custom.ForgotPasswordForm.index.' . $attrName . '.notice'),
        'collection' => $recoveryMethods,
        'selectedItem' => $defaultRecoveryMethod,
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

@endphp

<div id="{{ $attrName }}-widget" class="form-group @if ($hideWidget ?? '') d-none @endif">
    <label for="{{ $attrName }}" class="tab-item-label my-auto font-weight-bold">
        {{ $label }}
    </label>
    @if (!empty($notice))
        <label class="d-block col-sm-12 p-0"><span class='text-gray'>{!! $notice !!}</span></label>
    @endif

    @foreach ($collection as $itemValue => $itemLabel)
        <div class="form-check">
            <label class="form-check-label">
                <input type="radio" class="form-check-input" name="{{ $attrName }}"
                    id="{{ $attrName }}_{{ $itemValue }}" value="{{ $itemValue }}"
                    @if ($itemValue == $selectedItem) checked @endif>
                {{ $itemLabel }}
                <i class="input-helper"></i></label>
        </div>
    @endforeach

</div>
