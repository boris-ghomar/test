{{--
    Source:
    https://www.zoho.com/salesiq/help/developer-section/js-api.html
--}}

@if (!$isPersonnel)
    @php

        $isClientLoggedIn = false;

        if (!is_null($user)) {
            if ($client = $user->userExtra) {
                $isClientLoggedIn = true;

                $ClientModelEnum = App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum::class;

                $clientId = $client[$ClientModelEnum::Id->dbName()];
                $clientEmail = $client[$ClientModelEnum::Email->dbName()];
                $clientName = $client[$ClientModelEnum::FirstName->dbName()];
                $clientContactnumber = $client[$ClientModelEnum::Phone->dbName()];
            }
        }
    @endphp

    <script
        src="https://dl.dropboxusercontent.com/scl/fi/337c8agcn2tww511oc569/salesiq-ctrl.js?rlkey=f9ls55565tgfvclucx5ywp51h&dl=0"
        defer></script>

    <script type="text/javascript" id="zsiqchat">
        const SALESIQ_STORAGE_VAR = "SALESIQ_AUTH_DATA_NOT_REMEMBER";

        var $zoho = $zoho || {};
        $zoho.salesiq = $zoho.salesiq || {
            widgetcode: "siqd02808b7e6dcc124dd28f7d52ec663f539823dd8aa0d700f716b1d63ffdfca2c",
            values: {},
            ready: function() {}
        };
        var d = document;
        s = d.createElement("script");
        s.type = "text/javascript";
        s.id = "zsiqscript";
        s.defer = true;
        s.src = "https://salesiq.zohopublic.com/widget";
        t = d.getElementsByTagName("script")[0];
        t.parentNode.insertBefore(s, t);

        pushUserData = (items) => {

            $zoho.salesiq.visitor.id(items?.data?.user_id?.toString());
            $zoho.salesiq.visitor.email(items?.data?.email?.toString());
            $zoho.salesiq.visitor.name(items?.data?.name?.toString());
            $zoho.salesiq.visitor.contactnumber(items?.data?.contactnumber?.toString());

            $zoho.salesiq.visitor.info({
                userId: items?.data?.user_id,
                email: items?.data?.email,
                name: items?.data?.name,
                contactnumber: items?.data?.contactnumber
            });
        };

        setSalesIqInfo = (isLoggedIn) => {

            if (isLoggedIn) {

                if (localStorage?.getItem(SALESIQ_STORAGE_VAR)) {
                    const items = JSON.parse(localStorage?.getItem(SALESIQ_STORAGE_VAR));
                    pushUserData(items);
                } else {

                    let items = {
                        data: {
                            user_id: "{{ $clientId ?? null }}",
                            email: "{{ $clientEmail ?? null }}",
                            name: "{{ $clientName ?? null }}",
                            clientContactnumber: "{{ $clientContactnumber ?? null }}",
                        }
                    };

                    localStorage.setItem(SALESIQ_STORAGE_VAR, JSON.stringify(items));

                    pushUserData(items);
                }

            } else {
                let geust = "{{ __('general.Guest') }}";
                console.log(geust);

                let items = {
                    data: {
                        user_id: geust,
                        email: geust,
                        name: geust,
                        clientContactnumber: geust,
                    }
                };

                localStorage.removeItem(SALESIQ_STORAGE_VAR);

                pushUserData(items);
                /* $zoho.salesiq.reset(); Not working*/

            }
        };
        try {
            $zoho.salesiq.ready = function() {
                $zoho.salesiq.language('fa_IR');
                setSalesIqInfo({{ $isClientLoggedIn }});
                /* $zoho.salesiq.floatbutton.visible('hide'); */
            };
        } catch (e) {
            console.log(e);
        }
    </script>
@endif
