{{--
    Below items will be replaced by javascript

    ChatbotStep_Id
    ChatbotStep_TranslatedTypeName
 --}}

@php
    $ChatbotUserInputTypesEnum = App\Enums\Chatbot\ChatbotStepActions\ChatbotUserInputTypesEnum::class;
    $chatbotUserInputTypes = $ChatbotUserInputTypesEnum::translatedArray();
@endphp

{{-- sections --}}

@section('UserInput_Number')
    <div id="UserInputTypeForm_Number_ChatbotStep_Id" class="form-group">
        <div class="form-group">
            <label for="UserInputNumber_Title_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Title')</label>
            <input id="UserInputNumber_Title_ChatbotStep_Id" name="action[Data][Title_Num]" class="form-control" type="text"
                placeholder="@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Title')" value="StepUserInputNumberTitleValue">
        </div>
        <div class="form-group">
            <label for="UserInputNumber_Description_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Description')</label>
            <input id="UserInputNumber_Description_ChatbotStep_Id" name="action[Data][Description_Num]" class="form-control"
                type="text" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Description')" value="StepUserInputNumberDescriptionValue">
        </div>
        <div class="form-group">
            <label for="UserInputNumber_Placeholder_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Placeholder')</label>
            <input id="UserInputNumber_Placeholder_ChatbotStep_Id" name="action[Data][Placeholder_Num]" class="form-control"
                type="text" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Placeholder')" value="StepUserInputNumberPlaceholderValue">
        </div>
        <div class="form-group">
            <label for="UserInputNumber_Min_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Minimum')</label>
            <input id="UserInputNumber_Min_ChatbotStep_Id" name="action[Data][Min_Num]" class="form-control ltr"
                type="number" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Minimum')" value="StepUserInputNumberMinValue">
        </div>
        <div class="form-group">
            <label for="UserInputNumber_Max_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Maximum')</label>
            <input id="UserInputNumber_Max_ChatbotStep_Id" name="action[Data][Max_Num]" class="form-control ltr"
                type="number" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Maximum')" value="StepUserInputNumberMaxValue">
        </div>
        <div class="form-group">
            <div class="form-check">
                <label class="form-check-label">
                    <input type="checkbox" id="UserInputNumber_Required_ChatbotStep_Id"
                        name="action[Data][Required_Num]" step_user_input_number_required_checked
                        class="form-check-input" >@lang('thisApp.AdminPages.Chatbot.Form.UserInput.RequiredField')<i class="input-helper"></i>
                </label>
            </div>
        </div>
    </div>
@endsection

@section('UserInput_OneLineText')
    <div id="UserInputTypeForm_OneLineText_ChatbotStep_Id" class="form-group">
        <div class="form-group">
            <label for="UserInputOneLineText_Title_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Title')</label>
            <input id="UserInputOneLineText_Title_ChatbotStep_Id" name="action[Data][Title_OLT]" class="form-control"
                type="text" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Title')" value="StepUserInputOneLineTextTitleValue">
        </div>
        <div class="form-group">
            <label for="UserInputOneLineText_Description_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Description')</label>
            <input id="UserInputOneLineText_Description_ChatbotStep_Id" name="action[Data][Description_OLT]"
                class="form-control" type="text" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Description')"
                value="StepUserInputOneLineTextDescriptionValue">
        </div>
        <div class="form-group">
            <label for="UserInputOneLineText_Placeholder_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Placeholder')</label>
            <input id="UserInputOneLineText_Placeholder_ChatbotStep_Id" name="action[Data][Placeholder_OLT]"
                class="form-control" type="text" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Placeholder')"
                value="StepUserInputOneLineTextPlaceholderValue">
        </div>
        <div class="form-group">
            <label for="UserInputOneLineText_Min_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.UserInput.MinLength')</label>
            <input id="UserInputOneLineText_Min_ChatbotStep_Id" name="action[Data][MinLenght_OLT]" class="form-control ltr"
                type="number" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.UserInput.MinLength')" value="StepUserInputOneLineTextMinValue">
        </div>
        <div class="form-group">
            <label for="UserInputOneLineText_Max_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.UserInput.MaxLength')</label>
            <input id="UserInputOneLineText_Max_ChatbotStep_Id" name="action[Data][MaxLenght_OLT]" class="form-control ltr"
                type="number" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.UserInput.MaxLength')" value="StepUserInputOneLineTextMaxValue">
        </div>
        <div class="form-group">
            <div class="form-check">
                <label class="form-check-label">
                    <input type="checkbox" id="UserInputOneLineText_Required_ChatbotStep_Id"
                        name="action[Data][Required_OLT]" step_user_input_olt_required_checked
                        class="form-check-input" >@lang('thisApp.AdminPages.Chatbot.Form.UserInput.RequiredField')<i class="input-helper"></i>
                </label>
            </div>
        </div>
    </div>
@endsection

@section('UserInput_MultipleLineText')
    <div id="UserInputTypeForm_MultipleLineText_ChatbotStep_Id" class="form-group">
        <div class="form-group">
            <label for="UserInputMultipleLineText_Title_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Title')</label>
            <input id="UserInputMultipleLineText_Title_ChatbotStep_Id" name="action[Data][Title_MLT]" class="form-control"
                type="text" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Title')" value="StepUserInputMultipleLineTextTitleValue">
        </div>
        <div class="form-group">
            <label for="UserInputMultipleLineText_Description_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Description')</label>
            <input id="UserInputMultipleLineText_Description_ChatbotStep_Id" name="action[Data][Description_MLT]"
                class="form-control" type="text" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Description')"
                value="StepUserInputMultipleLineTextDescriptionValue">
        </div>
        <div class="form-group">
            <label for="UserInputMultipleLineText_Placeholder_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Placeholder')</label>
            <input id="UserInputMultipleLineText_Placeholder_ChatbotStep_Id" name="action[Data][Placeholder_MLT]"
                class="form-control" type="text" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Placeholder')"
                value="StepUserInputMultipleLineTextPlaceholderValue">
        </div>
        <div class="form-group">
            <label for="UserInputMultipleLineText_Min_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.UserInput.MinLength')</label>
            <input id="UserInputMultipleLineText_Min_ChatbotStep_Id" name="action[Data][MinLenght_MLT]"
                class="form-control ltr" type="number" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.UserInput.MinLength')"
                value="StepUserInputMultipleLineTextMinValue">
        </div>
        <div class="form-group">
            <label for="UserInputMultipleLineText_Max_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.UserInput.MaxLength')</label>
            <input id="UserInputMultipleLineText_Max_ChatbotStep_Id" name="action[Data][MaxLenght_MLT]"
                class="form-control ltr" type="number" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.UserInput.MaxLength')"
                value="StepUserInputMultipleLineTextMaxValue">
        </div>
        <div class="form-group">
            <div class="form-check">
                <label class="form-check-label">
                    <input type="checkbox" id="UserInputMultipleLineText_Required_ChatbotStep_Id"
                        name="action[Data][Required_MLT]" step_user_input_mlt_required_checked
                        class="form-check-input" >@lang('thisApp.AdminPages.Chatbot.Form.UserInput.RequiredField')<i class="input-helper"></i>
                </label>
            </div>
        </div>
    </div>
@endsection

@section('UserInput_Image')
    <div id="UserInputTypeForm_Image_ChatbotStep_Id" class="form-group">
        <div class="form-group">
            <label for="UserInputImage_Title_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Title')</label>
            <input id="UserInputImage_Title_ChatbotStep_Id" name="action[Data][Title_Img]" class="form-control"
                type="text" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Title')" value="StepUserInputImageTitleValue">
        </div>
        <div class="form-group">
            <label for="UserInputImage_Description_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Description')</label>
            <input id="UserInputImage_Description_ChatbotStep_Id" name="action[Data][Description_Img]"
                class="form-control" type="text" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.UserInput.Description')"
                value="StepUserInputImageDescriptionValue">
        </div>
        <div class="form-group">
            <div class="form-check">
                <label class="form-check-label">
                    <input type="checkbox" id="UserInputImage_Required_ChatbotStep_Id"
                        name="action[Data][Required_Img]" step_user_input_img_required_checked
                        class="form-check-input" >@lang('thisApp.AdminPages.Chatbot.Form.UserInput.RequiredField')<i class="input-helper"></i>
                </label>
            </div>
        </div>
    </div>
@endsection

{{-- sections END --}}

<div id="EditUserInputTemplate">

    <div style="position: absolute; z-index: 1000; min-width:500px;">
        <div class="card">
            <div class="card-body">
                <div class='close-hhh_modal'>
                    <span
                        onclick="chatbotCreator.displayView('editStepContainer_ChatbotStep_Id', false);">&times;</span>
                </div>
                <h4 class="card-title">ChatbotStep_TranslatedTypeName</h4>
                <p class="card-description">@lang('thisApp.AdminPages.Chatbot.Form.UserInput.IgnoreValidation')</p>

                <form id="StepForm_ChatbotStep_Id" class="forms-sample mb-3">

                    <div class="form-group">
                        <label for="StepTitle_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.StepTitle')</label>
                        <input id="StepTitle_ChatbotStep_Id" name="name" type="text" class="form-control"
                            placeholder="@lang('thisApp.AdminPages.Chatbot.Form.StepTitle')" value="StepTitleValue">
                    </div>

                    <div class="form-group">
                        <label for="StepUserInputType_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.UserInput.StepType')</label>
                        <select id="StepUserInputType_ChatbotStep_Id" class="form-control" name="action[Type]"
                            onchange="chatbotCreator.toggleBotUserInputForm(this, 'ChatbotStep_Id');">

                            @foreach ($chatbotUserInputTypes as $text => $key)
                                <option value="{{ $key }}">{{ $text }}</option>
                            @endforeach

                        </select>
                    </div>

                    {{-- User input Number Form --}}
                    @yield('UserInput_Number')

                    {{-- User input OneLineText Form --}}
                    @yield('UserInput_OneLineText')

                    {{-- User input MultipleLineText Form --}}
                    @yield('UserInput_MultipleLineText')

                    {{-- User input Image Form --}}
                    @yield('UserInput_Image')


                </form>

                <button type="submit" class="btn btn-primary me-2"
                    onclick="chatbotCreator.updateStep(ChatbotStep_Id);">@lang('general.Save')</button>
                <button class="btn btn-light"
                    onclick="chatbotCreator.displayView('editStepContainer_ChatbotStep_Id', false);chatbotCreator.createChatbotDiagram();">@lang('general.Cancel')</button>

            </div>
        </div>
    </div>

</div>
