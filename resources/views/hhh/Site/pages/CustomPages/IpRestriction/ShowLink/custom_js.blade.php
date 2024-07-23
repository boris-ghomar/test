<script>
    function copyUrl(siteLink) {

        navigator.clipboard.writeText(siteLink);

        var siteURLCopiedObj = document.getElementById('site_url_copied');
        siteURLCopiedObj.classList.remove('d-none');

    }
</script>
