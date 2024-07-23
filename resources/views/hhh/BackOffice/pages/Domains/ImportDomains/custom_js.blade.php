<script src="{{ url('assets/general/js/domains_importer.min.js') . $resourceVersion }}"></script>

<script>
    var domainsImporter;
    $(document).ready(function() {

        domainsImporter = new DomainsImporter(
            "{{ url(config('hhh_config.apiBaseUrls.backoffice.javascript')) }}", "{{ $payload }}",
            "DataDispalyTable", "domainsImporter", "DomainsListInput");
    });
</script>
