<script src="{{ url('assets/site/template/js/file-upload.js') . $resourceVersion }}"></script>
<script src="{{ url('assets/general/js/client_profile_controller.min.js') . $resourceVersion }}"></script>

<script>
    var modal_loading = new Modal_loading();

    var clientProfileCtl;
    var contactNumbersInternalCtl;
    $(document).ready(function() {

        clientProfileCtl = new ClientProfileController(
            "{{ url(config('hhh_config.apiBaseUrls.site.javascript')) }}");

        contactNumbersInternalCtl = new WidgetInputGroup('{{ $ClientExtrasTableEnum::ContactNumbersInternal->dbName() }}', 5);
    });
</script>

{{-- keep active tab --}}
@include('hhh.widgets.requirements.BootstrapTabs_KeepActiveTab')
{{-- keep active tab END --}}
