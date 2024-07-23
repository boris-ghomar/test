
<script src="{{ url('assets/site/template/js/file-upload.js') .'?version='. config('hhh_config.ResourceVersion') }}"></script>
<script>
    var modal_loading = new Modal_loading();
</script>

{{-- bootstrap_clockpicker --}}
<script type="text/javascript" src={{ url('assets/general/widgets/bootstrap_clockpicker/js/bootstrap-clockpicker.min.js') .'?version='. config('hhh_config.ResourceVersion') }}></script>
<script type="text/javascript" src={{ url('assets/general/widgets/bootstrap_clockpicker/js/bootstrap-clockpicker-settings.js') .'?version='. config('hhh_config.ResourceVersion') }}></script>
{{-- bootstrap_clockpicker END --}}

{{-- keep active tab --}}
@include('hhh.widgets.requirements.BootstrapTabs_KeepActiveTab')
{{-- keep active tab END --}}

