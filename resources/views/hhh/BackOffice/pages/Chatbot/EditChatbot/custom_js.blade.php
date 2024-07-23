@php
    $version = '?version=' . config('hhh_config.ResourceVersion');
@endphp

<script src="{{ url('assets/general/js/chatbot_creator.min.js') . $version }}"></script>

<script>

    var chatbotCreator = new ChatbotCreator(
        "{{ url(config('hhh_config.apiBaseUrls.backoffice.javascript')) }}",
        '{{ $chatbotId }}');
</script>
