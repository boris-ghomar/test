{{--
    Below items will be replaced by javascript


    TemplateViewID
    InputTemplate_Label
    InputTemplate_Placeholder
    InputTemplate_SubmitFunc
 --}}

<div id="InputNumberTemplate">

    <div class="form-group">
        <label for="MsgInput_TemplateViewID">InputTemplate_Label</label>

        <input id="MsgInput_TemplateViewID" type="number" class="form-control ltr" style="min-width: 200px;"
            autocomplete="off" placeholder="InputTemplate_Placeholder">

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

<div id="InputNumberPassedTemplate">

    <div class="form-group">
        <label for="MsgInput_TemplateViewID">InputTemplate_Label</label>

        <input id="MsgInput_TemplateViewID" type="number" class="form-control ltr" style="min-width: 200px;"
            autocomplete="off" placeholder="" value="InputPassedTemplate_UserAnswer" disabled>

        <div id="MsgStatusView_TemplateViewID" class="mt-2">
            <i class="fa-duotone fa-check text-success"></i>
        </div>

    </div>

</div>
