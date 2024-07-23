<script src="{{ url('assets/general/js/chatbot_messenger.min.js') . $resourceVersion }}"></script>

<script>
    var chatbotMessenger;
    $(document).ready(function() {

        chatbotMessenger = new ChatbotMessenger(
            "{{ url(config('hhh_config.apiBaseUrls.site.javascript')) }}", "{{ $payload }}",
            "chatbotMessageContainer", "chatbotMessenger");
    });
</script>
