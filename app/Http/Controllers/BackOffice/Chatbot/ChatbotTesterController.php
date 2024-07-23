<?php

namespace App\Http\Controllers\BackOffice\Chatbot;


use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\ChatbotsTableEnum;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Enums\Database\Tables\ChatbotTestersTableEnum as TableEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\Http\Requests\BackOffice\Chatbot\ChatbotTesterRequest;
use App\Http\Resources\BackOffice\Chatbot\ChatbotTesterCollection;
use App\Http\Resources\BackOffice\Chatbot\ChatbotTesterResource;
use App\Models\BackOffice\Chatbot\Chatbot;
use App\Models\BackOffice\Chatbot\ChatbotTester;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotTesterController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $this->authorize(PermissionAbilityEnum::viewAny->name, ChatbotTester::class);

        $jsGrid_Controller = parent::getJsGridType(ChatbotTester::class);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("chatbots/testers");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'bc_username'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $attributes = (new ChatbotTesterRequest())->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::UserId->dbName(), __("thisApp.UserId"));
        $fieldMaker->makeField_Number();
        $attr = $attributes[TableEnum::UserId->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "140");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('bc_username', __("general.UserName"));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "left");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::ChatbotId->dbName(), __("thisApp.AdminPages.Chatbot.ChatbotName"));
        $options = DropdownListCreater::makeByModel(Chatbot::class, ChatbotsTableEnum::Name->dbName())
            ->prepend("", -1)->useLable("name", "id")->sort(true)->get();
        $fieldMaker->makeField_Select('id', 'name', -1, 'number', $options);
        $attr = $attributes[TableEnum::ChatbotId->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value > 0;}", trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, null);
        $fieldMaker->makeField_Control();
        $jsGrid_Controller->putField($fieldMaker);


        $data = [
            config('hhh_config.keywords.jsGridJavaData')    =>  $jsGrid_Controller->create(),
        ];


        return view('hhh.BackOffice.pages.Chatbot.ChatbotTesters.index', $data);
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
     * @param \App\Http\Requests\BackOffice\Chatbot\ChatbotTesterRequest $request
     * @param \App\Models\BackOffice\Chatbot\ChatbotTester $chatbotTester
     * @return  \Illuminate\Http\JsonResponse
     */
    public function store(ChatbotTesterRequest $request, ChatbotTester $chatbotTester): JsonResponse
    {
        try {

            $item = new ChatbotTester();

            $item->fill($request->all());

            $item->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new ChatbotTesterResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Display the specified resource.
     */
    public function show(ChatbotTester $chatbotTester)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChatbotTester $chatbotTester)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\BackOffice\Chatbot\ChatbotTesterRequest $request
     * @param \App\Models\BackOffice\Chatbot\ChatbotTester $chatbotTester
     * @return  \Illuminate\Http\JsonResponse
     */
    public function update(ChatbotTesterRequest $request, ChatbotTester $chatbotTester): JsonResponse
    {
        try {

            if ($item = ChatbotTester::find($request->input(TableEnum::Id->dbName()))) {

                $item->fill($request->all());
                $item->save();
                return JsonResponseHelper::successResponse(new ChatbotTesterResource($item), null, HttpResponseStatusCode::Created->value);
            }
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::errorResponse(null, trans('general.NotFoundItem'), HttpResponseStatusCode::BadRequest->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\BackOffice\Chats\ChatbotTesterRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(ChatbotTesterRequest $request): JsonResponse
    {
        if ($item = ChatbotTester::find($request->input(TableEnum::Id->dbName()))) {

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

        $this->authorize(PermissionAbilityEnum::viewAny->name, ChatbotTester::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new ChatbotTesterCollection(
            ChatbotTester::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }
}
