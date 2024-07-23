{{--
    Below items will be replaced by javascript

    AddNewStepTemplate_dropdownMenuButton
 --}}
<div id="AddNewStepTemplate">
    <div class="d-flex justify-content-center">

        <div class="dropdown">
            <button type="button" class="btn btn-secondary btn-rounded btn-fw" id="AddNewStepTemplate_dropdownMenuButton"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                style="width: 50px; height: auto; display: flex; flex-direction: column; align-items: center;">
                <i class="fa-solid fa-plus"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="AddNewStepTemplate_dropdownMenuButton"
                style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 49px);"
                data-popper-placement="bottom-start">


                @foreach ($ChatbotStepTypesEnum::translatedArray() as $chatbotStepText => $chatbotStepType)
                    <button class="dropdown-item"
                        onclick="chatbotCreator.addNewStep('{{ $chatbotStepType }}','AddNewStepTemplate_ParentID');">{{ $chatbotStepText }}</button>
                @endforeach
            </div>
        </div>
    </div>
</div>
