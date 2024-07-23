<?php

namespace App\Http\Controllers\BackOffice\Chatbot;


use App\Enums\AccessControl\PermissionAbilityEnum;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Enums\Database\Tables\ChatbotsTableEnum as TableEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\Http\Requests\BackOffice\Chatbot\ChatbotRequest;
use App\Http\Resources\BackOffice\Chatbot\ChatbotCollection;
use App\Http\Resources\BackOffice\Chatbot\ChatbotResource;
use App\Models\BackOffice\Chatbot\Chatbot;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, Chatbot::class);

        $jsGrid_Controller = parent::getJsGridType(Chatbot::class);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("chatbots/bots_management");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'name'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $attributes = (new ChatbotRequest())->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Name->dbName(), __("general.Name"));
        $fieldMaker->makeField_Text();
        $attr = $attributes[TableEnum::Name->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::IsActive->dbName(), __("general.isActive"));
        $fieldMaker->makeField_Checkbox();
        $jsGrid_Controller->putField($fieldMaker);

        if (User::authUser()->can(PermissionAbilityEnum::update->name, Chatbot::class)) {

            $fieldMaker = new jsGrid_FieldMaker('edit_btn', __('general.buttons.Edit'));
            $fieldMaker->makeField_Text();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_ShowOnly);
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
            $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Descr->dbName(), __('general.Description'));
        $fieldMaker->makeField_Textarea();
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "300");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, null);
        $fieldMaker->makeField_Control();
        $jsGrid_Controller->putField($fieldMaker);


        $data = [
            config('hhh_config.keywords.jsGridJavaData')    =>  $jsGrid_Controller->create(),
        ];


        return view('hhh.BackOffice.pages.Chatbot.Chatbots.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\BackOffice\Chatbot\ChatbotRequest $request
     * @param \App\Models\BackOffice\Chatbot\Chatbot $chatbot
     * @return  \Illuminate\Http\JsonResponse
     */
    public function store(ChatbotRequest $request, Chatbot $chatbot): JsonResponse
    {
        try {

            $item = new Chatbot();

            $item->fill($request->all());

            $item->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new ChatbotResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Display the specified resource.
     */
    public function show(Chatbot $chatbot)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chatbot $chatbot)
    {
        $this->authorize(PermissionAbilityEnum::update->name, Chatbot::class);

        $data = [
            'chatbotId' => $chatbot[TableEnum::Id->dbName()],
        ];

        return view('hhh.BackOffice.pages.Chatbot.EditChatbot.index', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\BackOffice\Chatbot\ChatbotRequest $request
     * @param \App\Models\BackOffice\Chatbot\Chatbot $chatbot
     * @return  \Illuminate\Http\JsonResponse
     */
    public function update(ChatbotRequest $request, Chatbot $chatbot): JsonResponse
    {
        try {

            if ($item = Chatbot::find($request->input(TableEnum::Id->dbName()))) {

                $item->fill($request->all());
                $item->save();
                return JsonResponseHelper::successResponse(new ChatbotResource($item), null, HttpResponseStatusCode::Created->value);
            }
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::errorResponse(null, trans('general.NotFoundItem'), HttpResponseStatusCode::BadRequest->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\BackOffice\Chats\ChatbotRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(ChatbotRequest $request): JsonResponse
    {
        if ($item = Chatbot::find($request->input(TableEnum::Id->dbName()))) {

            $item->delete();
            return JsonResponseHelper::successResponse(null, trans('general.ItemSuccessfullyRemoved'));
        }

        return JsonResponseHelper::errorResponse(null, trans('general.NotFoundItem'), HttpResponseStatusCode::BadRequest->value);
    }

    /**
     * Return the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function apiIndex(Request $request)
    {

        $this->authorize(PermissionAbilityEnum::viewAny->name, Chatbot::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new ChatbotCollection(
            Chatbot::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }
}
