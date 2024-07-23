<script src="{{ url('assets/general/js/registration_controller.min.js') . $resourceVersion }}"></script>

<script>
    var modal_loading = new Modal_loading();

    var registrationCtl;
    var contactNumbersInternalCtl;
    $(document).ready(function() {

        registrationCtl = new RegistrationController(
            "{{ url(config('hhh_config.apiBaseUrls.site.javascript')) }}");

        contactNumbersInternalCtl = new WidgetInputGroup(
            '{{ $ClientExtrasTableEnum::ContactNumbersInternal->dbName() }}', 5);

    });
</script>
