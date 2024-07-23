{{--
    Below items will be replaced by javascript

    ChatbotStep_Id
    ChatbotStep_TranslatedTypeName
    StepTitleValue
 --}}

<div id="MoveBotStepTemplate">

    <div style="position: absolute; z-index: 1000; width:max-content;min-width:200px;">
        <div class="card">
            <div class="card-body">
                <div class='close-hhh_modal'>
                    <span onclick="chatbotCreator.displayView('moveStepContainer_ChatbotStep_Id', false);">&times;</span>
                </div>
                <h4 class="card-title">ChatbotStep_TranslatedTypeName</h4>
                <p class="card-description text-white-75">StepTitleValue</p>

                <form id="StepMoveForm_ChatbotStep_Id" class="forms-sample mb-3">

                    <div class="form-group">
                        <label for="StepMoveType_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.MoveType')</label>
                        <select id="StepMoveType_ChatbotStep_Id" class="form-control" name="moveType">

                            <option value="under" selected>@lang('thisApp.AdminPages.Chatbot.MoveUnder')</option>
                            <option value="after">@lang('thisApp.AdminPages.Chatbot.MoveAfter')</option>
                            <option value="before">@lang('thisApp.AdminPages.Chatbot.MoveBefore')</option>

                        </select>
                    </div>


                    <div class="form-group">
                        <label for="StepMoveTragetStep_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.StepId')</label>
                        <input id="StepMoveTragetStep_ChatbotStep_Id" type="number" class="form-control ltr"
                            name="moveTragetStepId" placeholder="@lang('thisApp.AdminPages.Chatbot.StepId')">
                    </div>

                </form>

                <button type="submit" class="btn btn-primary me-2"
                    onclick="chatbotCreator.moveStep(ChatbotStep_Id);">@lang('general.Save')</button>
                <button class="btn btn-light"
                    onclick="chatbotCreator.displayView('moveStepContainer_ChatbotStep_Id', false);chatbotCreator.createChatbotDiagram();">@lang('general.Cancel')</button>

            </div>
        </div>
    </div>

</div>
