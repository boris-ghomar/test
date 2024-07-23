@if (session('domains'))
    @php
        $domainsJson = session('domains');
        $domains = json_decode(session('domains'));
        $domainsListClipboard = implode("\n", $domains);
    @endphp

    <script>
        function copyDomains() {

            let domains = JSON.parse('{!! $domainsJson !!}');
            let domainList = "";

            domains.forEach(domain => {

                domainList += domain + "\n";
            });

            navigator.clipboard.writeText(domainList);

            var siteURLCopiedObj = document.getElementById('copied_message');
            siteURLCopiedObj.classList.remove('d-none');

        }
    </script>
@endif
