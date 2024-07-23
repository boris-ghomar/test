<?php

namespace App\Http\Controllers\BackOffice\Domains;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\DomainHolderAccountsTableEnum as TableEnum;
use App\Enums\Database\Tables\DomainHoldersTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\hhh_Exports\Domains\DomainHolderAccountExport;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\Domains\DomainHolderAccountRequest;
use App\Http\Resources\BackOffice\Domains\DomainHolderAccountCollection;
use App\Http\Resources\BackOffice\Domains\DomainHolderAccountResource;
use App\Models\BackOffice\Domains\DomainHolder;
use App\Models\BackOffice\Domains\DomainHolderAccount;
use App\Models\User;
use Illuminate\Http\Request;

class DomainHolderAccountController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $this->authorize(PermissionAbilityEnum::viewAny->name, DomainHolderAccount::class);

        $jsGrid_Controller = parent::getJsGridType(DomainHolderAccount::class);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("domains/holders_accounts");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'name'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $domainHolderAccountRequest = new DomainHolderAccountRequest();
        $attributes = $domainHolderAccountRequest->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::DomainHolderId->dbName(), __('thisApp.AdminPages.DomainsHoldersAccounts.domainHolder'));
        $attr = $attributes[TableEnum::DomainHolderId->dbName()];
        $options = DropdownListCreater::makeByModel(DomainHolder::class, DomainHoldersTableEnum::Name->dbName())
            ->prepend("", -1)
            ->useLable("name", "id")->sort(true)->get();
        $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value > 0;}", trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "left");
        $fieldMaker->makeField_Select("id", "name", 0, "number", $options);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker("domain_holder_url", __('general.URL'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "left");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Username->dbName(), __('general.UserName'));
        $fieldMaker->makeField_Text();
        $attr = $attributes[TableEnum::Username->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->addValidate($fieldMaker::validator_maxLength, 255, trans('validation.max.string', ['attribute' => $attr, 'max' => 255]));
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "left");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Email->dbName(), __('general.Email'));
        $fieldMaker->makeField_Text();
        $attr = $attributes[TableEnum::Email->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_maxLength, 255, trans('validation.max.string', ['attribute' => $attr, 'max' => 255]));
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "left");
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
            config('hhh_config.keywords.useExcelExport')    => User::authUser()->can(PermissionAbilityEnum::export->name, DomainHolderAccount::class),
        ];

        return view('hhh.BackOffice.pages.Domains.DomainsHoldersAccounts.index', $data);
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
     * @param  \App\Http\Requests\BackOffice\Domains\DomainHolderAccountRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DomainHolderAccountRequest $request)
    {
        try {
            $domainHolderAccount = new DomainHolderAccount();

            $domainHolderAccount->fill($request->all());

            LogCreator::createLogInfo(
                __CLASS__,
                __FUNCTION__,
                __('logs.domainHolderAccount.store', $this->addPadToArrayVal([
                    'domainHolderName'      => $domainHolderAccount->domainHolder[DomainHoldersTableEnum::Name->dbName()],
                    'domainHolderUsername'  => $domainHolderAccount[TableEnum::Username->dbName()],
                    'authUser'              => User::authUser()[UsersTableEnum::Username->dbName()]
                ])),
                'New domain holder account has been added'
            );

            $domainHolderAccount->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new DomainHolderAccountResource($domainHolderAccount), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BackOffice\Domains\DomainHolderAccount  $domainHolderAccount
     * @return \Illuminate\Http\Response
     */
    public function show(DomainHolderAccount $domainHolderAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BackOffice\Domains\DomainHolderAccount  $domainHolderAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(DomainHolderAccount $domainHolderAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\Domains\DomainHolderAccountRequest  $request
     * @param  \App\Models\BackOffice\Domains\DomainHolderAccount  $domainHolderAccount
     * @return \Illuminate\Http\Response
     */
    public function update(DomainHolderAccountRequest $request, DomainHolderAccount $domainHolderAccount)
    {

        try {
            $domainHolderAccount = DomainHolderAccount::find($request->input('id'));
            $domainHolderAccount->fill($request->all());

            LogCreator::attachModelRequestComparison($request, $domainHolderAccount)
                ->createLogInfo(
                    __CLASS__,
                    __FUNCTION__,
                    __('logs.domainHolderAccount.update', $this->addPadToArrayVal([
                        'domainHolderName'      => $domainHolderAccount->domainHolder[DomainHoldersTableEnum::Name->dbName()],
                        'domainHolderUsername'  => $domainHolderAccount[TableEnum::Username->dbName()],
                        'authUser'              => User::authUser()[UsersTableEnum::Username->dbName()]
                    ])),
                    'Domain holder account has been updated'
                );

            $domainHolderAccount->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new DomainHolderAccountResource($domainHolderAccount), null, HttpResponseStatusCode::Accepted->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\Domains\DomainHolderAccountRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(DomainHolderAccountRequest $request)
    {
        if ($domainHolderAccount = DomainHolderAccount::find($request->input('id'))) {

            LogCreator::createLogNotice(
                __CLASS__,
                __FUNCTION__,
                __('logs.domainHolderAccount.destroy', $this->addPadToArrayVal([
                    'domainHolderName'      => $domainHolderAccount->domainHolder[DomainHoldersTableEnum::Name->dbName()],
                    'domainHolderUsername'  => $domainHolderAccount[TableEnum::Username->dbName()],
                    'authUser'              => User::authUser()[UsersTableEnum::Username->dbName()],
                    'details'               => json_encode($domainHolderAccount, JSON_PRETTY_PRINT)
                ])),
                'Domain holder account has been deleted'
            );

            $domainHolderAccount->delete();
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
        $this->authorize(PermissionAbilityEnum::viewAny->name, DomainHolderAccount::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new DomainHolderAccountCollection(
            DomainHolderAccount::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }

    /**
     * This function exports the table information
     * to an Excel file and downloads it to the user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \App\HHH_Library\general\php\traits\ApiResponser  ApiResponse
     */
    public function exportExcel(Request $request)
    {
        $this->authorize(PermissionAbilityEnum::export->name, DomainHolderAccount::class);

        $exporter = new DomainHolderAccountExport($request->all());
        return $exporter->export();
    }
}
