<script src="{{ url('assets/site/template/js/file-upload.js') .'?version='. config('hhh_config.ResourceVersion') }}"></script>

<script>
    var modal_loading = new Modal_loading();
</script>


{{-- keep active tab --}}
@include('hhh.widgets.requirements.BootstrapTabs_KeepActiveTab')
{{-- keep active tab END --}}

<script>
    $(document).ready(function() {
        addCalculateItemLenghtEvent(
            'title', 'title_length',
            {{ SeoMetaTagsEnum::MetaTitle->minLength() }},
            {{ SeoMetaTagsEnum::MetaTitle->maxLength() }}
        );
    });

    function addCalculateItemLenghtEvent(inputId, displayId, min, max) {

        var inputObj = document.getElementById(inputId);
        var displayObj = document.getElementById(displayId);

        calculateItemLenght(inputObj, displayObj, min, max);

        inputObj.addEventListener('input', function(event) {
            calculateItemLenght(inputObj, displayObj, min, max);
        });

    }

    function calculateItemLenght(inputObj, displayObj, min, max) {

        var length = inputObj.value.length;
        displayObj.innerHTML = length;

        var successClass = "text-success";
        var dangerClass = "text-danger";

        if (length < min || length > max) {

            if (displayObj.classList.contains(successClass))
                displayObj.classList.remove(successClass);

            if (!displayObj.classList.contains(dangerClass))
                displayObj.classList.add(dangerClass);

        } else {
            if (displayObj.classList.contains(dangerClass))
                displayObj.classList.remove(dangerClass);

            if (!displayObj.classList.contains(successClass))
                displayObj.classList.add(successClass);
        }
    }
</script>
