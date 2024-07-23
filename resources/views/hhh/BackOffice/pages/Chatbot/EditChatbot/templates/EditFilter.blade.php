{{--
    Below items will be replaced by javascript

    ChatbotStep_Id
    ChatbotStep_TranslatedTypeName
 --}}

@php
    $ChatbotFilterTypesEnum = App\Enums\Chatbot\ChatbotStepActions\ChatbotFilterTypesEnum::class;
    $chatbotFilterTypes = $ChatbotFilterTypesEnum::translatedArray();

    $dropdownListCreaterClass = App\HHH_Library\general\php\DropdownListCreater::class;
    $clientCategoryClass = App\Models\BackOffice\ClientsManagement\ClientCategory::class;
    $rolesTableEnumClass = App\Enums\Database\Tables\RolesTableEnum::class;
    $clientCategories = $dropdownListCreaterClass
        ::makeByModel($clientCategoryClass, $rolesTableEnumClass::Name->dbName())
        ->prepend(__('thisApp.GuestUser'), -1)
        ->useLable('name', 'id')
        ->sort()
        ->get();
@endphp

{{-- sections --}}

@section('Filter_ClientCategory')
    <div id="FilterTypeForm_ClientCategory_ChatbotStep_Id" class="form-group">
        <div class="form-group">
            <label for="FilterClientCategory_AllowedCategories_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.Filter.AllowedClientCategories')</label>

            <div id="FilterClientCategory_AllowedCategories_ChatbotStep_Id">
                @foreach ($clientCategories as $clientCategory)
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox"
                                id="FilterClientCategory_ChatbotStep_Id_Checkbox_{{ $clientCategory['id'] }}"
                                name="action[Data][AllowedCategories][{{ $clientCategory['id'] }}]"
                                class="form-check-input">{{ $clientCategory['name'] }}<i class="input-helper"></i>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection


{{-- sections END --}}

<div id="EditFilterTemplate">

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
                        <label for="StepFilterType_ChatbotStep_Id">@lang('thisApp.AdminPages.Chatbot.Form.Filter.StepType')</label>
                        <select id="StepFilterType_ChatbotStep_Id" class="form-control" name="action[Type]"
                            onchange="chatbotCreator.toggleBotFilterForm(this, 'ChatbotStep_Id');">

                            @foreach ($chatbotFilterTypes as $text => $key)
                                <option value="{{ $key }}">{{ $text }}</option>
                            @endforeach

                        </select>
                    </div>

                    {{-- Filter ClientCategory Form --}}
                    @yield('Filter_ClientCategory')


                </form>

                <button type="submit" class="btn btn-primary me-2"
                    onclick="chatbotCreator.updateStep(ChatbotStep_Id);">@lang('general.Save')</button>
                <button class="btn btn-light"
                    onclick="chatbotCreator.displayView('editStepContainer_ChatbotStep_Id', false);chatbotCreator.createChatbotDiagram();">@lang('general.Cancel')</button>

            </div>
        </div>
    </div>

</div>
