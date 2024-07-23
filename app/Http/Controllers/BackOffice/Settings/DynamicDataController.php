<?php

namespace App\Http\Controllers\BackOffice\Settings;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Enums\Database\Tables\DynamicDatasTableEnum as TableEnum;
use App\Enums\Settings\DynamicDataVariablesEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_Controller;
use App\Http\Requests\BackOffice\Settings\DynamicDataRequest;
use App\Http\Resources\BackOffice\Settings\DynamicDataCollection;
use App\Http\Resources\BackOffice\Settings\DynamicDataResource;
use App\Models\BackOffice\Settings\DynamicData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DynamicDataController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, DynamicData::class);

        $this->updateDynamicDataTabel();

        $jsGrid_Controller = parent::getJsGridType(DynamicData::class, jsGrid_Controller::jsGridType_Edit);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("settings/dynamic_data");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'var_name'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $attributes = (new DynamicDataRequest())->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::VarName->dbName(), __("thisApp.DynamicData.VarName"));
        $options = DropdownListCreater::makeByArray(DynamicDataVariablesEnum::translatedArray())
            ->prepend("", "")->useLable("name", "key")->sort(true)->get();
        $fieldMaker->makeField_Select('key', 'name', "", 'string', $options);
        $attr = $attributes[TableEnum::VarName->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value != '';}", trans('validation.required', ['attribute' => $attr]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::VarValue->dbName(), __("thisApp.DynamicData.VarValue"));
        $fieldMaker->makeField_Textarea();
        $attr = $attributes[TableEnum::VarValue->dbName()];
        // $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "300");
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


        return view('hhh.BackOffice.pages.Settings.DynamicDatas.index', $data);
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
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(DynamicData $dynamicData)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DynamicData $dynamicData)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\BackOffice\Settings\DynamicDataRequest $request
     * @param \App\Models\BackOffice\Settings\DynamicData $dynamicData
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(DynamicDataRequest $request, DynamicData $dynamicData): JsonResponse
    {
        try {

            $item = DynamicData::find($request->input(TableEnum::Id->dbName()));

            $item->fill($request->all());
            $item->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new DynamicDataResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DynamicData $dynamicData)
    {
        //
    }

    /**
     * Return the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function apiIndex(Request $request)
    {

        $this->authorize(PermissionAbilityEnum::viewAny->name, DynamicData::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new DynamicDataCollection(
            DynamicData::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }

    /**
     * Clear database table and update base on Enum
     *
     * @return void
     */
    private function updateDynamicDataTabel(): void
    {
        $this->clearDeletedData();
        $this->updateData();
    }

    /**
     * Clear settings table from deleted settings in Enums
     *
     * @return void
     */
    private function clearDeletedData(): void
    {
        foreach (DynamicData::all() as $item) {

            if (!DynamicDataVariablesEnum::hasName($item[TableEnum::VarName->dbName()]))
                $item->delete();
        }
    }

    /**
     * Update settings table in database whit new items in enum
     *
     * @return void
     */
    private function updateData(): void
    {
        foreach (DynamicDataVariablesEnum::cases() as $case) {

            // To avoid of update existing items, only insert not exist item
            if (!DynamicData::itemExists($case))
                DynamicData::set($case, null);
        }
    }
}
