@php
    $ChatbotStepTypesEnum = App\Enums\Chatbot\ChatbotStepTypesEnum::class;
@endphp

<div id="diagramContainer" style="display: grid; overflow: hidden; min-height: 75vh; cursor: grab;">


    <div class="d-flex justify-content-center">

        <div class="">

            <div id="startPoint" class="d-flex justify-content-center">
                <button type="button" class="btn btn-secondary btn-rounded btn-fw">@lang('thisApp.AdminPages.Chatbot.StartPoint')</button>
            </div>

            <div id="mainCanvas"></div>
        </div>
    </div>
</div>

{{-- These items are used to create a new view by JavaScript. --}}
<Section name="templates" class="d-none">

    @include('hhh.BackOffice.pages.Chatbot.EditChatbot.templates.VerticalLine')
    @include('hhh.BackOffice.pages.Chatbot.EditChatbot.templates.HorizantalLine')
    @include('hhh.BackOffice.pages.Chatbot.EditChatbot.templates.AddNewStep')
    @include('hhh.BackOffice.pages.Chatbot.EditChatbot.templates.HorizontalContainer')

    @include('hhh.BackOffice.pages.Chatbot.EditChatbot.templates.BotStep')
    @include('hhh.BackOffice.pages.Chatbot.EditChatbot.templates.MoveStep')
    @include('hhh.BackOffice.pages.Chatbot.EditChatbot.templates.EditBotResponse')
    @include('hhh.BackOffice.pages.Chatbot.EditChatbot.templates.RandomTextItem')
    @include('hhh.BackOffice.pages.Chatbot.EditChatbot.templates.EditUserInput')
    @include('hhh.BackOffice.pages.Chatbot.EditChatbot.templates.EditFilter')
    @include('hhh.BackOffice.pages.Chatbot.EditChatbot.templates.EditBotAction')
</Section>

{{-- Translated Texts --}}
<input type="hidden" id="ChatbotConfirm_DeleteStep" value="@lang('thisApp.AdminPages.Chatbot.ChatbotConfirm.DeleteStep')">
