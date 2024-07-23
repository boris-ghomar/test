<?php

namespace App\Http\Controllers\BackOffice\ClientsManagement;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\ClientCategoryMapsTableEnum as TableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\SystemReserved\ClientCategoryReservedEnum;
use App\Enums\Users\ClientCategoryMapTypesEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\Http\Requests\BackOffice\ClientsManagement\ClientCategoryMapRequest;
use App\Http\Resources\BackOffice\ClientsManagement\ClientCategoryMapCollection;
use App\Http\Resources\BackOffice\ClientsManagement\ClientCategoryMapResource;
use App\Models\BackOffice\ClientsManagement\ClientCategory;
use App\Models\BackOffice\ClientsManagement\ClientCategoryMap;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientCategoryMapController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, ClientCategoryMap::class);

        $jsGrid_Controller = parent::getJsGridType(ClientCategoryMap::class);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("clients_management/category_maps");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, trans('confirm.Delete.simple'));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $attributes = (new ClientCategoryMapRequest())->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::MapType->dbName(), __("thisApp.AdminPages.ClientsManagement.MapType"));
        $options = DropdownListCreater::makeByArray(ClientCategoryMapTypesEnum::translatedArray())
            ->useLable("name", "key")->prepend("", "")
            ->sort(true)->get();
        $fieldMaker->makeField_Select('key', 'name', "", 'string', $options);
        $attr = $attributes[TableEnum::MapType->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value != '';}", trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::ItemValue->dbName(), __('thisApp.AdminPages.ClientsManagement.Value'));
        $fieldMaker->makeField_Text();
        $attr = $attributes[TableEnum::ItemValue->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::RoleId->dbName(), __("thisApp.ClientCategory"));
        $options = DropdownListCreater::makeByModel(ClientCategory::class, RolesTableEnum::Name->dbName())
            ->prepend("", -1)
            ->notAllowedTexts([ClientCategoryReservedEnum::NormalUser->value])
            ->useLable("name", "id")->sort(true)->get();
        $fieldMaker->makeField_Select('id', 'name', -1, 'number', $options);
        $attr = $attributes[TableEnum::RoleId->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value > 0;}", trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Priority->dbName(), __('thisApp.Priority'));
        $fieldMaker->makeField_Number();
        $attr = $attributes[TableEnum::Priority->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->addValidate($fieldMaker::validator_min, 1, trans('validation.min.numeric', ['attribute' => $attr, 'min' => 1]));
        $fieldMaker->setItemProperties($fieldMaker::field_Width, '100');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_isInserting, false);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::IsActive->dbName(), __('general.isActive'));
        $fieldMaker->makeField_Checkbox();
        $jsGrid_Controller->putField($fieldMaker);

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


        return view('hhh.BackOffice.pages.ClientsManagement.ClientCategoryMaps.index', $data);
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
     * @param \App\Http\Requests\BackOffice\ClientsManagement\ClientCategoryMapRequest $request
     * @param \App\Models\BackOffice\ClientsManagement\ClientCategoryMap $clientCategoryMap
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ClientCategoryMapRequest $request, ClientCategoryMap $clientCategoryMap): JsonResponse
    {
        try {
            $priorityCol = TableEnum::Priority->dbName();

            $item = new ClientCategoryMap();

            $item->fill($request->all());
            $lastPosition = ClientCategoryMap::select($priorityCol)->orderBy($priorityCol, 'desc')->first();

            $item->$priorityCol = (is_null($lastPosition)) ? 1 : $lastPosition->$priorityCol + 1;

            $item->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new ClientCategoryMapResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Display the specified resource.
     */
    public function show(ClientCategoryMap $clientCategoryMap)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClientCategoryMap $clientCategoryMap)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\BackOffice\ClientsManagement\ClientCategoryMapRequest $request
     * @param \App\Models\BackOffice\ClientsManagement\ClientCategoryMap $clientCategoryMap
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ClientCategoryMapRequest $request, ClientCategoryMap $clientCategoryMap): JsonResponse
    {
        try {

            $priorityCol = TableEnum::Priority->dbName();

            $item = ClientCategoryMap::find($request->input(TableEnum::Id->dbName()));

            $item->fill($request->all());
            if ($isDirtyPriority = $item->isDirty($priorityCol))
                $lastPriority = $item->getOriginal($priorityCol);
            $item->save();

            if ($isDirtyPriority)
                $this->adjustPriorities($lastPriority, $item->$priorityCol);
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new ClientCategoryMapResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\BackOffice\ClientsManagement\ClientCategoryMapRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(ClientCategoryMapRequest $request): JsonResponse
    {
        if ($item = ClientCategoryMap::find($request->input(TableEnum::Id->dbName()))) {

            $item->delete();
            $this->adjustPriorities(0, 0);
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

        $this->authorize(PermissionAbilityEnum::viewAny->name, ClientCategoryMap::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new ClientCategoryMapCollection(
            ClientCategoryMap::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }

    /**
     * Adjust items Priorities
     *
     * @param  int $lastPriority
     * @param  int $currentPosition
     * @return void
     */
    private function adjustPriorities(int $lastPriority, int $currentPriority): void
    {
        $idCol = TableEnum::Id->dbName();
        $priorityCol = TableEnum::Priority->dbName();

        $updatedAtSort = ($currentPriority < $lastPriority) ? "desc" : "asc";

        $items = ClientCategoryMap::select($idCol, $priorityCol)
            ->orderBy($priorityCol, 'asc')
            ->orderBy(TimestampsEnum::UpdatedAt->dbName(), $updatedAtSort)
            ->get();

        $priority = 1;
        foreach ($items as $item) {

            $item->$priorityCol = $priority;
            $item->save();

            $priority++;
        }
    }
}
