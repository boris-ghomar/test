{{-- blade-formatter-disable --}}

{{--
@param $attrId :  unique id for element
@param $jsonString : json string
--}}

{{-- Example: --}}

{{--
@include('back_office.widgets.documentation.jsonViewer',
[
'attrId' => 'supported-protocol',
'jsonString' => '{"key1":"<script>alert(\'no xss!\')</script>","key2":12345,"key3":"2022-02-15T08:38:43.704Z","key4":[],"key5":[123,"123",{"a":5,"b":6,"c":null,"d":true}],"key6":{"a":1,"b":3,"c":{"d":4}}}',
])
--}}



<textarea id="json-content-{{ $attrId }}" class="json-viewer-textarea">{{ $jsonString }}</textarea>

<div id="json-view-{{ $attrId }}"></div>

<script>
    var jsonViewerController = new JsonViewerController('json-content-{{ $attrId }}', 'json-view-{{ $attrId }}', 0);
    jsonViewerController.createView();
</script>
{{-- blade-formatter-enable --}}
