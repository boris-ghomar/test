{{--
    Below items will be replaced by javascript


    TemplateViewID
    InputTemplate_Label
    InputTemplate_Placeholder
    InputTemplate_SubmitFunc
 --}}

<div id="InputMultipleLineTextTemplate">

    <div class="form-group">
        <label for="MsgInput_TemplateViewID">InputTemplate_Label</label>

        <textarea id="MsgInput_TemplateViewID" cols="30" rows="10" class="form-control" autocomplete="off"
            placeholder="InputTemplate_Placeholder"></textarea>

        <div class="submit-section">
            <button id="MsgSubmitBtn_TemplateViewID" type="button" class="btn btn-primary mt-2"
                onclick="InputTemplate_SubmitFunc">@lang('thisApp.Buttons.Send')</button>
        </div>

        <div id="MsgStatusView_TemplateViewID" class="mt-2">
            {{-- Proccess: <i class="fa-duotone fa-spinner fa-spin-pulse"></i> --}}
            {{-- Successful: <i class="fa-duotone fa-check text-success"></i> --}}
            {{-- Failed: <i class="fa-solid fa-xmark text-danger"></i> --}}
        </div>

    </div>

</div>


{{--
    Below items will be replaced by javascript


    InputTemplate_Label
    InputPassedTemplate_UserAnswer
 --}}

<div id="InputMultipleLineTextPassedTemplate">

    <div class="form-group">
        <label for="MsgInput_TemplateViewID">InputTemplate_Label</label>

        <textarea id="MsgInput_TemplateViewID" cols="30" rows="10" class="form-control" autocomplete="off" disabled>InputPassedTemplate_UserAnswer</textarea>


        <div id="MsgStatusView_TemplateViewID" class="mt-2">
            <i class="fa-duotone fa-check text-success"></i>
        </div>

    </div>

</div>
