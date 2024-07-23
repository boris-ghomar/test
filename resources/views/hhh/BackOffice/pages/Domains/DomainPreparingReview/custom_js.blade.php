<script src="{{ url('assets/general/js/domain_preparing_reviewer.min.js') . $resourceVersion }}"></script>

<script>
    var domainPreparingReviewer;
    $(document).ready(function() {

        domainPreparingReviewer = new DomainPreparingReviewer(
            "{{ url(config('hhh_config.apiBaseUrls.backoffice.javascript')) }}", "domainPreparingReviewer");
    });
</script>
