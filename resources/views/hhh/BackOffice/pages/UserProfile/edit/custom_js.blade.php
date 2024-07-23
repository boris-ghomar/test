
<script src="{{ url('assets/site/template/js/file-upload.js') .'?version='. config('hhh_config.ResourceVersion') }}"></script>

<script>
    var modal_loading = new Modal_loading();
</script>

{{-- keep active tab --}}
@include('hhh.widgets.requirements.BootstrapTabs_KeepActiveTab')
{{-- keep active tab END --}}
