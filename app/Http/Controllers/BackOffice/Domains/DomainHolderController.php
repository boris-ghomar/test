<?php

namespace App\Http\Controllers\BackOffice\Domains;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\DomainHoldersTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\Domains\DomainHolderRequest;
use App\Http\Resources\BackOffice\Domains\DomainHolderCollection;
use App\Http\Resources\BackOffice\Domains\DomainHolderResource;
use App\Models\BackOffice\Domains\DomainHolder;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DomainHolderController extends SuperJsGridController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, DomainHolder::class);

        $jsGrid_Controller = parent::getJsGridType(DomainHolder::class);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("domains/holders");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'name'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $domainHolderRequest = new DomainHolderRequest();
        $attributes = $domainHolderRequest->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Name->dbName(), __('general.Name'));
        $fieldMaker->makeField_Text();
        $attr = $attributes[TableEnum::Name->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->addValidate($fieldMaker::validator_maxLength, 255, trans('validation.max.string', ['attribute' => $attr, 'max' => 255]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Url->dbName(), __('general.URL'));
        $fieldMaker->makeField_Text();
        $attr = $attributes[TableEnum::Url->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->addValidate($fieldMaker::validator_maxLength, 255, trans('validation.max.string', ['attribute' => $attr, 'max' => 255]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::IsActive->dbName(), __('general.isActive'));
        $fieldMaker->makeField_Checkbox();
        $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
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

        return view('hhh.BackOffice.pages.Domains.DomainsHolders.index', $data);
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
     * @param  \App\Http\Requests\BackOffice\Domains\DomainHolderRequest  $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function store(DomainHolderRequest $request): JsonResponse
    {
        try {
            $domainHolder = new DomainHolder();

            $domainHolder->fill($request->all());

            LogCreator::createLogInfo(
                __CLASS__,
                __FUNCTION__,
                __('logs.domainHolder.store', $this->addPadToArrayVal([
                    'domainHolderName'      => $domainHolder[TableEnum::Name->dbName()],
                    'authUser'  => User::authUser()[UsersTableEnum::Username->dbName()]
                ])),
                'New domain holder has been added'
            );

            $domainHolder->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new DomainHolderResource($domainHolder), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BackOffice\Domains\DomainHolder  $domainHolder
     * @return \Illuminate\Http\Response
     */
    public function show(DomainHolder $domainHolder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BackOffice\Domains\DomainHolder  $domainHolder
     * @return \Illuminate\Http\Response
     */
    public function edit(DomainHolder $domainHolder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\Domains\DomainHolderRequest  $request
     * @param  \App\Models\BackOffice\Domains\DomainHolder  $domainHolder
     * @return \Illuminate\Http\Response
     */
    public function update(DomainHolderRequest $request, DomainHolder $domainHolder)
    {
        try {
            $domainHolder = DomainHolder::find($request->input('id'));

            $domainHolder->id = $request->input('id');
            $domainHolder->fill($request->all());

            LogCreator::attachModelRequestComparison($request, $domainHolder)
                ->createLogInfo(
                    __CLASS__,
                    __FUNCTION__,
                    __('logs.domainHolder.update', $this->addPadToArrayVal([
                        'domainHolderName'  => $domainHolder[TableEnum::Name->dbName()],
                        'authUser'          => User::authUser()[UsersTableEnum::Username->dbName()]
                    ])),
                    'Domain holder has been updated'
                );

            $domainHolder->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new DomainHolderResource($domainHolder), null, HttpResponseStatusCode::Accepted->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\Domains\DomainHolderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(DomainHolderRequest $request)
    {
        if ($domainHolder = DomainHolder::find($request->input('id'))) {

            LogCreator::createLogNotice(
                __CLASS__,
                __FUNCTION__,
                __('logs.domainHolder.destroy', $this->addPadToArrayVal([
                    'name'      => $domainHolder[TableEnum::Name->dbName()],
                    'authUser'  => User::authUser()[UsersTableEnum::Username->dbName()],
                    'details'   => json_encode($domainHolder, JSON_PRETTY_PRINT)
                ])),
                'Domain holder has been deleted'
            );

            $domainHolder->delete();
            return JsonResponseHelper::successResponse(null, trans('general.ItemSuccessfullyRemoved'));
        }
    }

    /**
     * Return the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BackOffice\office  $office
     * @return \Illuminate\Http\Response
     */
    public function apiIndex(Request $request)
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, DomainHolder::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new DomainHolderCollection(
            DomainHolder::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }
}
