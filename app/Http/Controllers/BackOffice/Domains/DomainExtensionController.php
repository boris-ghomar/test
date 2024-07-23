<?php

namespace App\Http\Controllers\BackOffice\Domains;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\DomainExtensionsTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\Domains\DomainExtensionRequest;
use App\Http\Resources\BackOffice\Domains\DomainExtensionCollection;
use App\Http\Resources\BackOffice\Domains\DomainExtensionResource;
use App\Models\BackOffice\Domains\DomainExtension;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DomainExtensionController extends SuperJsGridController
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, DomainExtension::class);

        $jsGrid_Controller = parent::getJsGridType(DomainExtension::class);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("domains/extensions");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'name'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $domainExtensionRequest = new DomainExtensionRequest();
        $attributes = $domainExtensionRequest->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Name->dbName(), __('general.Name'));
        $fieldMaker->makeField_Text();
        $attr = $attributes[TableEnum::Name->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->addValidate($fieldMaker::validator_maxLength, 255, trans('validation.max.string', ['attribute' => $attr, 'max' => 255]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::LimitedOrder->dbName(), __('thisApp.AdminPages.DomainExtension.limitedOrder'));
        $fieldMaker->makeField_Checkbox();
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

        return view('hhh.BackOffice.pages.Domains.DomainsExtensions.index', $data);
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
     * @param  \App\Http\Requests\BackOffice\Domains\DomainExtensionRequest  $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function store(DomainExtensionRequest $request): JsonResponse
    {
        try {
            $domainExtension = new DomainExtension();
            $domainExtension->fill($request->all());

            LogCreator::createLogInfo(
                __CLASS__,
                __FUNCTION__,
                __('logs.domainExtension.store', $this->addPadToArrayVal([
                    'name'      => $domainExtension[TableEnum::Name->dbName()],
                    'authUser'  => User::authUser()[UsersTableEnum::Username->dbName()]
                ])),
                'New domain extension has been added'
            );

            $domainExtension->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new DomainExtensionResource($domainExtension), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BackOffice\Domains\DomainExtension  $domainExtension
     */
    public function show(DomainExtension $domainExtension)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BackOffice\Domains\DomainExtension  $domainExtension
     */
    public function edit(DomainExtension $domainExtension)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\Domains\DomainExtensionRequest  $request
     * @param  \App\Models\BackOffice\Domains\DomainExtension  $domainExtension
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(DomainExtensionRequest $request, DomainExtension $domainExtension): JsonResponse
    {
        try {
            $domainExtension = DomainExtension::find($request->input('id'));
            $domainExtension->fill($request->all());

            LogCreator::attachModelRequestComparison($request, $domainExtension)
                ->createLogInfo(
                    __CLASS__,
                    __FUNCTION__,
                    __('logs.domainExtension.update', $this->addPadToArrayVal([
                        'name'      => $domainExtension[TableEnum::Name->dbName()],
                        'authUser'  => User::authUser()[UsersTableEnum::Username->dbName()]
                    ])),
                    'Domain extension has been updated'
                );

            $domainExtension->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new DomainExtensionResource($domainExtension), null, HttpResponseStatusCode::Accepted->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\Domains\DomainExtensionRequest  $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(DomainExtensionRequest $request): JsonResponse
    {
        if ($domainExtension = DomainExtension::find($request->input('id'))) {

            LogCreator::createLogNotice(
                __CLASS__,
                __FUNCTION__,
                __('logs.domainExtension.destroy', $this->addPadToArrayVal([
                    'name'      => $domainExtension[TableEnum::Name->dbName()],
                    'authUser'  => User::authUser()[UsersTableEnum::Username->dbName()],
                    'details'   => json_encode($domainExtension, JSON_PRETTY_PRINT)
                ])),
                'Domain extension has been deleted'
            );

            $domainExtension->delete();
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
        $this->authorize(PermissionAbilityEnum::viewAny->name, DomainExtension::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new DomainExtensionCollection(
            DomainExtension::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }
}
