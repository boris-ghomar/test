{{--
 @param $attrName
--}}
@error($attrName)
    <div class="alert alert-danger">
        <ul class="p-0 m-0">
            @foreach ($errors->get($attrName) as $error)
                <li style="text-align:start;">
                    {{ $error }}
                </li>
            @endforeach
        </ul>
    </div>
@enderror
