@php
    $resourceVersion = '?version=' . config('hhh_config.ResourceVersion');
@endphp
{{-- Important files --}}

{{--
    Attention:
    These items may interfere with other jQueries, so they should be listed after all of them.

    such as:
        "back_office/assets/vendors/js/vendor.bundle.base.js"
--}}

{{-- ChartJs View --}}
<script>
    (function($) {
        'use strict';
        $(function() {
            if ($("#{{ $containerId }}").length) {

                let ctx = document.getElementById('{{ $containerId }}');

                let chartConfigJson = {!! $chartConfig !!};

                let chartConfig = JSON.parse(JSON.stringify(chartConfigJson), function(key, value) {

                    if (typeof value === "string" && value.startsWith("hhh_java(") && value
                        .endsWith(")")) {

                        value = value.substring(9, value.length - 1);
                        return new Function(value);
                    }
                    return value;
                });

                new Chart(ctx, chartConfig);
            }

        });

    })(jQuery);
</script>
