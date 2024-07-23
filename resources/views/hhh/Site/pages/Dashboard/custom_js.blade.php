<script src="{{ url('assets/general/js/site_dashboard.min.js') . $resourceVersion }}"></script>

<script>
    var domainCtl;
    $(document).ready(function() {

        domainCtl = new DomainController("{{ url(config('hhh_config.apiBaseUrls.site.javascript')) }}");
    });
</script>
