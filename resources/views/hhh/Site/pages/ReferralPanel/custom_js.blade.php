<script src="{{ url('assets/general/widgets/charts/chart_js/js/chart.min.js') . $resourceVersion }}"></script>
{!! $referredPerformanceChartScript !!}
{!! $rewardPerformanceChartScript !!}

<script>
    var modal_loading = new Modal_loading();

    function copyReferralLink(link) {
        func_copyToClipboard(link);
        showSuccessToast(func_getView('ReferralLinkCopiedMsg').value);
    }
</script>
