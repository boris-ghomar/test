<div id="chatbotMessageContainer" class="chatbot chat-page-messages"></div>

<button type="button" class="btn btn-rounded chatbot exit-chat" onclick="chatbotMessenger.closeChat();">
    <i class="fa-solid fa-xmark"></i>
</button>

{{-- These items are used to create a new view by JavaScript. --}}
<Section name="templates" class="d-none">

    @include('hhh.Site.pages.Chatbot.ChatbotMessenger.templates.Loading')
    @include('hhh.Site.pages.Chatbot.ChatbotMessenger.templates.Message')
    @include('hhh.Site.pages.Chatbot.ChatbotMessenger.templates.Profiles')

    @include('hhh.Site.pages.Chatbot.ChatbotMessenger.templates.InputNumber')
    @include('hhh.Site.pages.Chatbot.ChatbotMessenger.templates.InputOneLineText')
    @include('hhh.Site.pages.Chatbot.ChatbotMessenger.templates.InputMultipleLineText')
    @include('hhh.Site.pages.Chatbot.ChatbotMessenger.templates.InputImage')

</Section>

{{-- Translated Texts --}}
<input type="hidden" id="ChatbotMessenger_ImageRemoved" value="@lang('thisApp.Errors.ChatbotMessenger.imageRemoved')">
