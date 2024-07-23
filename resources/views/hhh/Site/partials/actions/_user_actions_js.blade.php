@php
    $version = '?version=' . config('hhh_config.ResourceVersion');
@endphp

<script src="{{ url('assets/general/js/post_action.min.js') . $version }}"></script>

<script type="text/javascript">
    var postAction;
    $(document).ready(function() {
        postAction = new PostAction('{{ url(config('hhh_config.apiBaseUrls.site.javascript')) }}',
            {{ config('app.debug') }});

        postAction.goToAnchorLink();
    });


    /* Translations */
    var loginPageUrl = "{{ SitePublicRoutesEnum::defaultLogin()->route() }}";
    var loginReqired = "@lang('thisApp.Errors.loginRequired')";
    var signInBtnLabel = "@lang('auth_site.custom.SignIn')";

    var successCommentTitle = "@lang('thisApp.PostActions.successCommentTitle')";
</script>
