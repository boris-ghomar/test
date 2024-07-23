{{--
    @param $attrName
    @param $label
    @param $notice
    @param $placeholder
    @param $value

    -optinal-
    @param $style ?? '' -optinal-
    @param $disabled ?? '': true | false -optinal- default: false
    @param $minlength ?? '': -optinal- Example: $minlength=2
    @param $maxlength ?? '': -optinal- Example: $maxlength=10
    @param $hideWidget ?? '': true | false -optinal- default: false
    @param $disableAutocomplete ?? '': true | false -optinal- default: false
--}}

{{-- Example: --}}

{{--

--}}

<div id="{{ $attrName }}-widget" class="form-group @if ($hideWidget ?? '') d-none @endif">
    <label for="{{ $attrName }}" class="tab-item-label my-auto font-weight-bold">{{ $label }}</label>
    @if (!empty($notice))
        <label class="d-block col-sm-12 p-0"><span class='text-gray'>{!! $notice !!}</span></label>
    @endif
    <div class="input-group @error($attrName) is-invalid @enderror">
        <div class="input-group-prepend bg-transparent">
            <span class="input-group-text bg-transparent border-0">
                <i id="{{ $attrName }}_hidden" class="mdi mdi-eye text-primary"
                    onclick="togglePasswordVisibility('{{ $attrName }}','{{ $attrName }}_show','{{ $attrName }}_hidden')"></i>
                <i id="{{ $attrName }}_show" class="mdi mdi-eye-off text-primary d-none"
                    onclick="togglePasswordVisibility('{{ $attrName }}','{{ $attrName }}_show','{{ $attrName }}_hidden')"></i>
            </span>
        </div>
        <input class="ltr form-control form-control-lg border-0" id="{{ $attrName }}"
            @if ($disableAutocomplete ?? '') autocomplete="new-password" @else autocomplete="current-password" @endif
            placeholder="{{ $placeholder }}" type="password" name="{{ $attrName }}" style="{{ $style ?? '' }}"
            @if ($disabled ?? '') disabled @endif
            @if ($minlength ?? '') minlength="{{ $minlength }}" @endif
            @if ($maxlength ?? '') maxlength="{{ $maxlength }}" @endif
            value="{{ old($attrName, $value) }}">
    </div>
    @include('hhh.widgets.messages.ShowFieldAllErrors')
</div>
