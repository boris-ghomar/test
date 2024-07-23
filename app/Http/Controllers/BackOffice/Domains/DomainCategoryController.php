<?php

namespace App\Http\Controllers\BackOffice\Domains;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\DomainCategoriesTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\Domains\DomainCategoryRequest;
use App\Http\Resources\BackOffice\Domains\DomainCategoryCollection;
use App\Http\Resources\BackOffice\Domains\DomainCategoryResource;
use App\Models\BackOffice\Domains\DomainCategory;
use App\Models\User;
use Illuminate\Http\Request;

class DomainCategoryController extends SuperJsGridController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, DomainCategory::class);

        $jsGrid_Controller = parent::getJsGridType(DomainCategory::class);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("domains/categories");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'name'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $domainCategoryRequest = new DomainCategoryRequest();
        $attributes = $domainCategoryRequest->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Name->dbName(), __("general.Name"));
        $fieldMaker->makeField_Text();
        $attr = $attributes[TableEnum::Name->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::DomainAssignment->dbName(), __('thisApp.AdminPages.Domains.DomainAssignment'));
        $fieldMaker->makeField_Checkbox();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::IsActive->dbName(), __('general.isActive'));
        $fieldMaker->makeField_Checkbox();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Descr->dbName(), __('general.Description'));
        $fieldMaker->makeField_Textarea();
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "300");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __('general.Icon'));
        $fieldMaker->makeField_Control();
        $jsGrid_Controller->putField($fieldMaker);


        $data = [
            config('hhh_config.keywords.jsGridJavaData')    =>  $jsGrid_Controller->create(),
        ];


        return view('hhh.BackOffice.pages.Domains.DomainCategories.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\Domains\DomainCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DomainCategoryRequest $request)
    {
        try {
            $domainCategory = new DomainCategory();

            $domainCategory->fill($request->all());

            LogCreator::createLogInfo(
                __CLASS__,
                __FUNCTION__,
                __('logs.domainCategory.store', $this->addPadToArrayVal([
                    'domainCategory'    => $domainCategory[TableEnum::Name->dbName()],
                    'authUser'          => User::authUser()[UsersTableEnum::Username->dbName()]
                ])),
                'New domain category has been added'
            );

            $domainCategory->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new DomainCategoryResource($domainCategory), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BackOffice\Domains\DomainCategory  $domainCategory
     * @return \Illuminate\Http\Response
     */
    public function show(DomainCategory $domainCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BackOffice\Domains\DomainCategory  $domainCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(DomainCategory $domainCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\Domains\DomainCategoryRequest  $request
     * @param  \App\Models\BackOffice\Domains\DomainCategory  $domainCategory
     * @return \Illuminate\Http\Response
     */
    public function update(DomainCategoryRequest $request)
    {
        try {
            $domainCategory = DomainCategory::find($request->input('id'));

            $domainCategory->id = $request->input('id');

            $domainCategory->fill($request->all());

            LogCreator::attachModelRequestComparison($request, $domainCategory)
                ->createLogInfo(
                    __CLASS__,
                    __FUNCTION__,
                    __('logs.domainCategory.update', $this->addPadToArrayVal([
                        'domainCategory'    => $domainCategory[TableEnum::Name->dbName()],
                        'authUser'          => User::authUser()[UsersTableEnum::Username->dbName()]
                    ])),
                    'Domain category has been updated'
                );

            $domainCategory->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new DomainCategoryResource($domainCategory), null, HttpResponseStatusCode::Accepted->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\Domains\DomainCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(DomainCategoryRequest $request)
    {
        if ($domainCategory = DomainCategory::find($request->input('id'))) {

            LogCreator::createLogNotice(
                __CLASS__,
                __FUNCTION__,
                __('logs.domainCategory.destroy', $this->addPadToArrayVal([
                    'domainCategory'    => $domainCategory[TableEnum::Name->dbName()],
                    'authUser'          => User::authUser()[UsersTableEnum::Username->dbName()],
                    'details'           => json_encode($domainCategory, JSON_PRETTY_PRINT)
                ])),
                'Domain category has been deleted'
            );

            $domainCategory->delete();
            return JsonResponseHelper::successResponse(null, trans('general.ItemSuccessfullyRemoved'));
        }
    }

    /**
     * Return the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function apiIndex(Request $request)
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, DomainCategory::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new DomainCategoryCollection(
            DomainCategory::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }
}
