{{--
    When using Bootstrap tabs on a page,
    you can save the last tab used by the user
    in the session by including this file
    in "custom_js.blade.php" file of page.
--}}
{{--
    Example:

    -- keep active tab --
    @include('back_office.widgets.requirements.BootstrapTabs_KeepActiveTab')
    -- keep active tab END --
--}}

{{-- keep active tab --}}
<script type="text/javascript">
    $(document).ready(function() {

        let currentUrl = window.location.href;
        let urlSplit = currentUrl.split('#');

        let tabName = null;
        if (urlSplit.length > 1) {
            tabName = urlSplit[urlSplit.length - 1];

            if (!func_isEmpty(tabName)) {
                sessionStorage.setItem('activeTab', '#' + tabName);
            }
        }

        $('.nav-tabs a').click(function() {

            let tabAnchorLink = $(this).attr('href');
            sessionStorage.setItem('activeTab', tabAnchorLink);
            window.location.href = urlSplit[0] + tabAnchorLink;
        });

        var activeTab = sessionStorage.getItem('activeTab');
        if (activeTab) {
            window.location.href = urlSplit[0] + activeTab;
            $('a[href="' + activeTab + '"]').tab('show');
        } else {
            $(".nav-tabs a:first").tab('show');
        }

    });
</script>
{{-- keep active tab END --}}
