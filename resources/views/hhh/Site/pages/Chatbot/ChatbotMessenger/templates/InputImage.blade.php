{{--
    Below items will be replaced by javascript


    TemplateViewID
    InputTemplate_Label
    InputTemplate_Placeholder
    InputTemplate_SubmitFunc
    TemplateClassHandlerVar
 --}}

<div id="InputImageTemplate">

    <div class="form-group">
        <label>InputTemplate_Label</label>

        <div id="MsgFileUploadInfo_TemplateViewID" class="input-group col-xs-12">

            {{-- file-upload-info --}}
            <input id="MsgInputFileName_TemplateViewID" type="text" class="form-control file-upload-info h-auto"
                disabled="">
            <span class="input-group-append">
                <button class="file-upload-browse btn btn-gradient-primary" type="button"
                    onclick="document.getElementById('MsgInput_TemplateViewID').click();">@lang('general.buttons.ChooseFile')</button>
            </span>

        </div>

        <div id="MsgImagePreview_TemplateViewID" class="userinput-img"></div>

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


    {{-- File Container Input --}}
    <input type="file" id="MsgInput_TemplateViewID" name="upload_file" class="d-none" accept=".jpg,.png"
        onchange="TemplateClassHandlerVar.userinputImageFileChanged(this,'TemplateViewID');">


</div>


{{--
    Below items will be replaced by javascript


    InputTemplate_Label
    InputImageTemplate_View
 --}}

<div id="InputImagePassedTemplate">

    <div class="form-group">
        <label for="MsgInput_TemplateViewID">InputTemplate_Label</label>

        <div class="userinput-img">
            InputImageTemplate_View
        </div>

        <div id="MsgStatusView_TemplateViewID" class="mt-2">
            <i class="fa-duotone fa-check text-success"></i>
        </div>

    </div>

</div>
