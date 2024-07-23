{{--
    Below items will be replaced by javascript

    ChatbotStepTemplate_StepID
    ChatbotStepTemplate_TranslatedTypeName
    ChatbotStepTemplate_Name
 --}}
<div id="BotStepTemplate">
    <div class="d-flex justify-content-center">

        <div class="dropdown">
            <button type="button" id="Step_ChatbotStepTemplate_StepID_dropdownMenuButton"
                class="btn btn-secondary btn-rounded btn-fw" style="width: 140px; min-width: 140px;"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <div class="mb-2 font-weight-bold">ChatbotStepTemplate_TranslatedTypeName</div>
                <div class="mb-2" style="color: darkslateblue;">@lang('general.ID'): ChatbotStepTemplate_StepID</div>
                <div style="color:darkgreen;">ChatbotStepTemplate_Name</div>
            </button>
            <div class="dropdown-menu" aria-labelledby="Step_ChatbotStepTemplate_StepID_dropdownMenuButton"
                style="position: absolute; z-index: 1001 ;inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 49px);"
                data-popper-placement="bottom-start">

                <button class="dropdown-item" onclick="chatbotCreator.moveStepDialog('ChatbotStepTemplate_StepID');"><i
                        class="fa-solid fa-arrows-up-down-left-right"></i> @lang('thisApp.Chatbot.StepActions.Move')</button>

                <button class="dropdown-item" onclick="chatbotCreator.editStep('ChatbotStepTemplate_StepID');"><i
                        class="fa-solid fa-pencil me-1"></i> @lang('thisApp.Chatbot.StepActions.Edit')</button>

                <button class="dropdown-item text-danger"
                    onclick="chatbotCreator.deleteStep('ChatbotStepTemplate_StepID','ChatbotStepTemplate_Name', false);"><i
                        class="fa-regular fa-trash-can me-1"></i> @lang('thisApp.Chatbot.StepActions.DeleteStep')</button>

                <button class="dropdown-item text-danger BotStepTemplate_Show_DeleteWithChilds"
                    onclick="chatbotCreator.deleteStep('ChatbotStepTemplate_StepID','ChatbotStepTemplate_Name', true);"><i
                        class="fa-regular fa-trash-can me-1"></i>
                    @lang('thisApp.Chatbot.StepActions.DeleteWithChilds')</button>

            </div>
            <div id="moveStepContainer_ChatbotStepTemplate_StepID" class="d-none"></div>
            <div id="editStepContainer_ChatbotStepTemplate_StepID" class="d-none"></div>
        </div>
    </div>

</div>
