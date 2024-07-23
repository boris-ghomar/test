{{--
    Below items will be replaced by javascript

    ChatbotStep_Id
    ChatbotStep_TranslatedTypeName
    StepResponseImage_FileName
 --}}

@php
    $ChatbotResponseTypesEnum = App\Enums\Chatbot\ChatbotStepActions\ChatbotResponseTypesEnum::class;
    $chatbotResponseTypes = $ChatbotResponseTypesEnum::translatedArray();
    $chatbotImageResponseConfig = App\Enums\Resources\ImageConfigEnum::ChatbotImageResponse;

    $chatbotImageResponsePath = url(App\Enums\Resources\ImageConfigEnum::ChatbotImageResponse->path());
@endphp

{{-- sections --}}
@section('BotResponse_Text')
    <div id="ResponseTypeForm_Text_ChatbotStep_Id" class="form-group">
        <label for="ResponseText_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.ResponseText')</label>
        <textarea id="ResponseText_ChatbotStep_Id" name="action[Data][TextValue]" class="form-control"
            placeholder="@lang('thisApp.AdminPages.Chatbot.Form.ResponseText')" rows="15">StepResponseTextValue</textarea>
    </div>
@endsection

@section('BotResponse_RandomText')
    <div id="ResponseTypeForm_RandomText_ChatbotStep_Id" class="form-group">

        <div class="mb-3">
            <div id="RandomTextCounterContainer_ChatbotStep_Id" style="display: inline-flex; flex-wrap: wrap;"></div>

            <button type="button" class="btn btn-secondary btn-rounded" style="height: 35px; width:35px;padding:0;"
                onclick="chatbotCreator.botRandomTextNewText('ChatbotStep_Id');"><i
                    class="fa fa-solid fa-plus"></i></button>
        </div>

        <div id="RandomTextInputSection_ChatbotStep_Id" class="d-none">
            <label for="ResponseRandomText_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.ResponseText')</label>
            <a id="RandomTextBtnDelItem_ChatbotStep_Id" type="button" class="text-danger" data-target-id=""
                onclick="chatbotCreator.deleteRandomText('ChatbotStep_Id', this);"><i class="fa-solid fa-trash-can"></i></a>

            <textarea id="RandomTextStepTextarea_ChatbotStep_Id" data-target-id="" class="form-control"
                placeholder="@lang('thisApp.AdminPages.Chatbot.Form.ResponseText')" rows="15"
                oninput="chatbotCreator.botRandomTextInputChanged('ChatbotStep_Id');"></textarea>

        </div>
    </div>
@endsection

@section('BotResponse_Image')
    <div id="ResponseTypeForm_Image_ChatbotStep_Id" class="form-group">
        <label for="ResponseImage_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.ResponseImage')</label>
        <input id="ResponseImageFormImageUpload_ChatbotStep_Id" type="file" name="ResponseImageFile"
            class="file-upload-default" accept="{{ $chatbotImageResponseConfig->acceptableMimesForUpload() }}"
            onchange="chatbotCreator.botResponseImageNewFileSelected('ChatbotStep_Id');">

        {{-- Holder of last name of file --}}
        <input name="action[Data][FileName]" type="hidden" value="StepResponseImage_FileName">
        <div id="ResponseImage_ChatbotStep_Id" class="input-group col-xs-12">
            <input id="ResponseImageFormImageUploadName_ChatbotStep_Id" type="text"
                class="form-control file-upload-info h-100" disabled="" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.ResponseImage')">
            <span class="input-group-append">
                <button class="file-upload-browse btn btn-primary" type="button"
                    onclick="chatbotCreator.botResponseImageUploadBtnClicked('ChatbotStep_Id');">@lang('general.buttons.ChooseFile')</button>
            </span>
        </div>

        <div id="StepResponseImage_DisplayImage_ChatbotStep_Id" class="mt-2 w-100 d-none">
            <span>@lang('thisApp.AdminPages.Chatbot.Form.CurrentImage')</span>
            <img src="{{ $chatbotImageResponsePath }}/StepResponseImage_FileName" style="width: 250px;height: auto;" />
        </div>

    </div>
@endsection

@section('BotResponse_Button')
    <div id="ResponseTypeForm_Button_ChatbotStep_Id" class="form-group">

        <div class="form-group">
            <label for="StepResponseButton_Title_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.ButtonTitle')</label>
            <input id="StepResponseButton_Title_ChatbotStep_Id" name="action[Data][Title]" class="form-control"
                placeholder="@lang('thisApp.AdminPages.Chatbot.Form.ButtonTitle')" value="StepResponseButton_TitleValue">
        </div>

        <div class="form-group">
            <label for="StepResponseButton_Type_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.ButtonType')</label>
            <select id="StepResponseButton_Type_ChatbotStep_Id" class="form-control" name="action[Data][Type]"
                onchange="chatbotCreator.toggleBotResponseButtonForm(this, 'ChatbotStep_Id');">

                @foreach (__('thisApp.AdminPages.Chatbot.Form.ButtonTypes') as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach

            </select>
        </div>

        {{-- Button types form sections --}}
        <div id="StepResponseButton_TypeSection_GoToStep_ChatbotStep_Id" class="form-group">
            <label for="StepResponseButton_Type_GoToStep_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.StepId')</label>
            <input id="StepResponseButton_Type_GoToStep_ChatbotStep_Id" name="action[Data][TargetStep]"
                class="form-control ltr" type="number" placeholder="@lang('thisApp.AdminPages.Chatbot.StepId')"
                value="StepResponseButton_TargetStepValue">
        </div>

        <div id="StepResponseButton_TypeSection_OpenUrl_ChatbotStep_Id" class="form-group">
            <label for="StepResponseButton_Type_OpenUrl_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.ButtonTypesUrl')</label>
            <input id="StepResponseButton_Type_OpenUrl_ChatbotStep_Id" name="action[Data][TargetUrl]"
                class="form-control ltr" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.ButtonTypesUrl')" value="StepResponseButton_TargetUrlValue">
        </div>

        {{-- Button types form sections END --}}

    </div>
@endsection
{{-- sections END --}}

<div id="EditBotResponseTemplate">

    <div style="position: absolute; z-index: 1000; min-width:500px;">
        <div class="card">
            <div class="card-body">
                <div class='close-hhh_modal'>
                    <span
                        onclick="chatbotCreator.displayView('editStepContainer_ChatbotStep_Id', false);">&times;</span>
                </div>
                <h4 class="card-title">ChatbotStep_TranslatedTypeName</h4>
                {{-- <p class="card-description">Bordered layout</p> --}}

                <form id="StepForm_ChatbotStep_Id" class="forms-sample mb-3">

                    <div class="form-group">
                        <label for="StepTitle_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.StepTitle')</label>
                        <input id="StepTitle_ChatbotStep_Id" name="name" type="text" class="form-control"
                            placeholder="@lang('thisApp.AdminPages.Chatbot.Form.StepTitle')" value="StepTitleValue">
                    </div>

                    <div class="form-group">
                        <label for="StepDelay_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.StepDelay')</label>
                        <input id="StepDelay_ChatbotStep_Id" name="action[Delay]" type="number"
                            class="form-control ltr" placeholder="@lang('thisApp.AdminPages.Chatbot.Form.StepDelay')" value="StepDelayValue">
                    </div>

                    <div class="form-group">
                        <label for="StepResponseType_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.StepType')</label>
                        <select id="StepResponseType_ChatbotStep_Id" class="form-control" name="action[Type]"
                            onchange="chatbotCreator.toggleBotResponseForm(this, 'ChatbotStep_Id');">

                            @foreach ($chatbotResponseTypes as $text => $key)
                                <option value="{{ $key }}">{{ $text }}</option>
                            @endforeach

                        </select>
                    </div>

                    {{-- Text Response Form --}}
                    @yield('BotResponse_Text')

                    {{-- RandomText Response Form --}}
                    @yield('BotResponse_RandomText')

                    {{-- Image Response Form --}}
                    @yield('BotResponse_Image')

                    {{-- Button Response Form --}}
                    @yield('BotResponse_Button')

                </form>

                <button type="submit" class="btn btn-primary me-2"
                    onclick="chatbotCreator.updateStep(ChatbotStep_Id);">@lang('general.Save')</button>
                <button class="btn btn-light"
                    onclick="chatbotCreator.displayView('editStepContainer_ChatbotStep_Id', false);chatbotCreator.createChatbotDiagram();">@lang('general.Cancel')</button>

            </div>
        </div>
    </div>

</div>
