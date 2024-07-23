{{--
    Below items will be replaced by javascript

    ChatbotStep_Id
    ChatbotStep_TranslatedTypeName
 --}}

@php
    $ChatbotActionTypesEnum = App\Enums\Chatbot\ChatbotStepActions\ChatbotActionTypesEnum::class;
    $chatbotActionTypes = $ChatbotActionTypesEnum::translatedArray();
@endphp

{{-- sections --}}

@section('BotAction_GoToStep')
    <div id="BotActionTypeForm_GoToStep_ChatbotStep_Id" class="form-group">

        <div class="form-group">
            <label for="BotActionGoToStep_TargetStep_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.StepId')</label>
            <input id="BotActionGoToStep_TargetStep_ChatbotStep_Id" name="action[Data][TargetStep]" class="form-control ltr"
                type="number" placeholder="@lang('thisApp.AdminPages.Chatbot.StepId')" value="BotActionGoToStep_TargetStepValue">
        </div>

    </div>
@endsection

@section('BotAction_StartTicket')
    <div id="BotActionTypeForm_StartTicket_ChatbotStep_Id" class="form-group">

        <div class="form-group">
            <label for="BotActionStartTicket_TicketSubject_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.BotAction.TicketSubject')</label>
            <input id="BotActionStartTicket_TicketSubject_ChatbotStep_Id" name="action[Data][TicketSubject]"
                class="form-control" type="text" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.BotAction.TicketSubject')"
                value="BotActionStartTicket_TicketSubjectValue">
        </div>

        <div class="form-group form-box">
            <label>@lang('thisApp.AdminPages.Chatbot.Form.BotAction.TicketSendingSchedule')</label>
            <p class="text-justify mb-2 text-enter">@lang('thisApp.AdminPages.Chatbot.Form.BotAction.TicketSendingScheduleDescr')</p>

            <div class="form-group">
                <label for="BotActionStartTicket_HourLimit_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.BotAction.HourLimit')</label>
                <input id="BotActionStartTicket_HourLimit_ChatbotStep_Id" name="action[Data][HourLimit]" type="number"
                    autocomplete="off" class="form-control" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.BotAction.HourLimit')"
                    value="BotActionStartTicket_HourLimitValue">
            </div>
            <div class="form-group">
                <label for="BotActionStartTicket_NumberLimit_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.BotAction.NumberLimit')</label>
                <input id="BotActionStartTicket_NumberLimit_ChatbotStep_Id" name="action[Data][NumberLimit]" type="number"
                    autocomplete="off" class="form-control" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.BotAction.NumberLimit')"
                    value="BotActionStartTicket_NumberLimitValue">
            </div>
            <div class="form-group">
                <label for="BotActionStartTicket_ScheduleFaildTargetStep_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.StepId')</label>

                <input id="BotActionStartTicket_ScheduleFaildTargetStep_ChatbotStep_Id"
                    name="action[Data][ScheduleFaildTargetStep]" type="number" autocomplete="off" class="form-control"
                    placeholder="@lang('thisApp.AdminPages.Chatbot.StepId')" value="BotActionStartTicket_ScheduleFaildTargetStepValue">
                <p class="text-justify">@lang('thisApp.AdminPages.Chatbot.Form.BotAction.ScheduleFaildTargetStep')</p>
            </div>
        </div>

    </div>
@endsection

@section('BotAction_MakeTicket')
    @php
        $TicketPrioritiesEnum = App\Enums\Tickets\TicketPrioritiesEnum::Class;
        $defaultPriority = $TicketPrioritiesEnum::Normal->name;
        $ticketPrioritiesDropdownList = $TicketPrioritiesEnum::translatedArray();
    @endphp

    <div id="BotActionTypeForm_MakeTicket_ChatbotStep_Id" class="form-group">

        <div class="form-group">
            <label for="BotActionMakeTicket_Priority_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.BotAction.TicketPriority')</label>
            <select id="BotActionMakeTicket_Priority_ChatbotStep_Id" class="form-control"
                name="action[Data][TicketPriority]" value="{{ $defaultPriority }}">

                @foreach ($ticketPrioritiesDropdownList as $text => $value)
                    <option value="{{ $value }}" @if ($value == $defaultPriority) selected @endif>
                        {{ $text }}</option>
                @endforeach

            </select>
        </div>

    </div>
@endsection


{{-- sections END --}}

<div id="EditBotActionTemplate">

    <div style="position: absolute; z-index: 1000; min-width:500px;">
        <div class="card">
            <div class="card-body">
                <div class='close-hhh_modal'>
                    <span
                        onclick="chatbotCreator.displayView('editStepContainer_ChatbotStep_Id', false);">&times;</span>
                </div>
                <h4 class="card-title">ChatbotStep_TranslatedTypeName</h4>
                {{-- <p class="card-description">Description</p> --}}

                <form id="StepForm_ChatbotStep_Id" class="forms-sample mb-3">

                    <div class="form-group">
                        <label for="StepTitle_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.StepTitle')</label>
                        <input id="StepTitle_ChatbotStep_Id" name="name" type="text" class="form-control"
                            placeholder="@lang('thisApp.AdminPages.Chatbot.Form.StepTitle')" value="StepTitleValue">
                    </div>

                    <div class="form-group">
                        <label for="StepFilterType_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.BotAction.StepType')</label>
                        <select id="StepFilterType_ChatbotStep_Id" class="form-control" name="action[Type]"
                            onchange="chatbotCreator.toggleBotActionForm(this, 'ChatbotStep_Id');">

                            @foreach ($chatbotActionTypes as $text => $key)
                                <option value="{{ $key }}">{{ $text }}</option>
                            @endforeach

                        </select>
                    </div>

                    {{-- BotAction GoToStep Form --}}
                    @yield('BotAction_GoToStep')

                    {{-- BotAction StartTicket Form --}}
                    @yield('BotAction_StartTicket')

                    {{-- BotAction MakeTicket Form --}}
                    @yield('BotAction_MakeTicket')


                </form>

                <button type="submit" class="btn btn-primary me-2"
                    onclick="chatbotCreator.updateStep(ChatbotStep_Id);">@lang('general.Save')</button>
                <button class="btn btn-light"
                    onclick="chatbotCreator.displayView('editStepContainer_ChatbotStep_Id', false);chatbotCreator.createChatbotDiagram();">@lang('general.Cancel')</button>

            </div>
        </div>
    </div>

</div>
