{{--
    @param resultDisplayId -optional- You might want to track the result message with JavaScript
--}}
@if ($errors->any())
    <div @if ($resultDisplayId ?? '') id="{{ $resultDisplayId }}_errors" @endif
        class="alert alert-danger justify-content-start text-justify">
        @lang('general.Error'):
        <ul class="p-0 m-0">
            @foreach ($errors->all() as $error)
                <li class="text-start ms-2">
                    {{ $error }}
                </li>
            @endforeach
        </ul>
    </div>
@endif


@if (session('success'))
    <div @if ($resultDisplayId ?? '') id="{{ $resultDisplayId }}_success" @endif
        class="alert alert-success justify-content-start text-justify">
        @lang('general.Success'): <br />
        {{ session('success') }}
    </div>
@endif

@if (session('warning'))
    <div @if ($resultDisplayId ?? '') id="{{ $resultDisplayId }}_warning" @endif
        class="alert alert-warning justify-content-start text-justify">
        @lang('general.Warning'): <br />
        {{ session('warning') }}
    </div>
@endif
