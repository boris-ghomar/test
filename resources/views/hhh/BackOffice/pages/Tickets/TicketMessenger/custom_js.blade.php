<script src="{{ url('assets/general/js/ticket_messenger.min.js') . $resourceVersion }}"></script>

<script>
    var ticketMessenger;
    $(document).ready(function() {

        ticketMessenger = new TicketMessenger(
            "{{ url(config('hhh_config.apiBaseUrls.backoffice.javascript')) }}", "{{ $payload }}",
            "ticketMessageContainer", "ticketMessenger");
    });
</script>
