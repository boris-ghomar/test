<?php

namespace App\Http\Controllers\BackOffice\Domains;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\DomainCategoriesTableEnum;
use App\Enums\Database\Tables\DomainHolderAccountsTableEnum;
use App\Enums\Database\Tables\DomainHoldersTableEnum;
use App\Enums\Database\Tables\DomainsTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Domains\DomainStatusEnum;
use App\hhh_Exports\Domains\DomainExport;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\Domains\DomainRequest;
use App\Http\Resources\BackOffice\Domains\DomainCollection;
use App\Http\Resources\BackOffice\Domains\DomainResource;
use App\Models\BackOffice\Domains\Domain;
use App\Models\BackOffice\Domains\DomainCategory;
use App\Models\BackOffice\Domains\DomainHolder;
use App\Models\BackOffice\Domains\DomainHolderAccount;
use App\Models\User;
use Illuminate\Http\Request;

class DomainController extends SuperJsGridController
{

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, Domain::class);

        $jsGrid_Controller = parent::getJsGridType(Domain::class);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("domains/all");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'name'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $domainRequest = new DomainRequest();
        $attributes = $domainRequest->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Name->dbName(), __('general.Name'));
        $fieldMaker->makeField_Text();
        $attr = $attributes[TableEnum::Name->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->addValidate($fieldMaker::validator_maxLength, 255, trans('validation.max.string', ['attribute' => $attr, 'max' => 255]));
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Status->dbName(), __("general.Status"));
        $options = DropdownListCreater::makeByArray(DomainStatusEnum::translatedArray())
            ->prepend("", "")->useLable("name", "key")->get();
        $fieldMaker->makeField_Select('key', 'name', "", 'string', $options);
        $attr = $attributes[TableEnum::Status->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value != '';}", trans('validation.required', ['attribute' => $attr]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::DomainCategoryId->dbName(), __('thisApp.AdminPages.Domains.domainCategory'));
        $options = DropdownListCreater::makeByModel(DomainCategory::class, DomainCategoriesTableEnum::Name->dbName())
            ->prepend("", -1)
            ->useLable("name", "id")->sort(true)->get();
        $attr = $attributes[TableEnum::DomainCategoryId->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value > 0;}", trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "center");
        $fieldMaker->makeField_Select("id", "name", 0, "number", $options);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(DomainHolderAccountsTableEnum::DomainHolderId->dbName(), __('thisApp.AdminPages.DomainsHoldersAccounts.domainHolder'));
        $options = DropdownListCreater::makeByModel(DomainHolder::class, DomainHoldersTableEnum::Name->dbName())
            ->prepend("", "")
            ->useLable("name", "id")->sort(true)->get();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "center");
        $fieldMaker->makeField_Select("id", "name", 0, "string", $options);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::DomainHolderAccountId->dbName(), __('thisApp.AdminPages.DomainsHoldersAccounts.domainHolderAccount'));
        $options = DropdownListCreater::makeByModel(DomainHolderAccount::class, DomainHolderAccountsTableEnum::Username->dbName())
            ->prepend("", "")
            ->useLable("name", "id")->sort(true)->get();
        $attr = $attributes[TableEnum::DomainHolderAccountId->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value != '';}", trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "center");
        $fieldMaker->makeField_Select("id", "name", 0, "string", $options);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::AutoRenew->dbName(), __('thisApp.AdminPages.Domains.autoRenew'));
        $fieldMaker->makeField_Checkbox();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::RegisteredAt->dbName(), __('thisApp.AdminPages.Domains.registeredAt'));
        $fieldMaker->makeField_DateRange();
        $attr = $attributes[TableEnum::RegisteredAt->dbName()];
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "center");
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::ExpiresAt->dbName(), __('thisApp.AdminPages.Domains.expiresAt'));
        $fieldMaker->makeField_DateRange();
        $attr = $attributes[TableEnum::ExpiresAt->dbName()];
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "center");
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::AnnouncedAt->dbName(), __('thisApp.AdminPages.Domains.announcedAt'));
        $fieldMaker->makeField_DateRange();
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "center");
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::BlockedAt->dbName(), __('thisApp.AdminPages.Domains.blockedAt'));
        $fieldMaker->makeField_DateRange();
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "center");
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Reported->dbName(), __('thisApp.AdminPages.Domains.Reported'));
        $fieldMaker->makeField_Checkbox();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Public->dbName(), __('thisApp.AdminPages.Domains.Public'));
        $fieldMaker->makeField_Checkbox();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Suspicious->dbName(), __('thisApp.AdminPages.Domains.SuspiciousClients'));
        $fieldMaker->makeField_Checkbox();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
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
            config('hhh_config.keywords.useExcelExport')    => User::authUser()->can(PermissionAbilityEnum::export->name, Domain::class),
        ];


        return view('hhh.BackOffice.pages.Domains.Domains.index', $data);
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
     * @param  \App\Http\Requests\BackOffice\Domains\DomainRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DomainRequest $request)
    {

        try {

            $domain = new Domain();
            $domain->fill($request->all());

            LogCreator::createLogInfo(
                __CLASS__,
                __FUNCTION__,
                __('logs.domains.store', $this->addPadToArrayVal([
                    'name'      => $domain[TableEnum::Name->dbName()],
                    'authUser'  => User::authUser()[UsersTableEnum::Username->dbName()]
                ])),
                'New domain has been added'
            );

            $domain->save();
        } catch (\Throwable $th) {

            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new DomainResource($domain), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BackOffice\Domains\Domain  $domain
     * @return \Illuminate\Http\Response
     */
    public function show(Domain $domain)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BackOffice\Domains\Domain  $domain
     * @return \Illuminate\Http\Response
     */
    public function edit(Domain $domain)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\Domains\DomainRequest  $request
     * @param  \App\Models\BackOffice\Domains\Domain  $domain
     * @return \Illuminate\Http\Response
     */
    public function update(DomainRequest $request, Domain $domain)
    {
        try {
            $domain = Domain::find($request->input('id'));
            $domain->fill($request->all());

            LogCreator::attachModelRequestComparison($request, $domain)
                ->createLogInfo(
                    __CLASS__,
                    __FUNCTION__,
                    __('logs.domains.update', $this->addPadToArrayVal([
                        'name'      => $domain[TableEnum::Name->dbName()],
                        'authUser'  => User::authUser()[UsersTableEnum::Username->dbName()]
                    ])),
                    'Domain has been updated'
                );

            $domain->save();
        } catch (\Throwable $th) {

            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new DomainResource($domain), null, HttpResponseStatusCode::Accepted->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\Domains\DomainRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(DomainRequest $request)
    {
        if ($domain = Domain::find($request->input('id'))) {

            LogCreator::createLogNotice(
                __CLASS__,
                __FUNCTION__,
                __('logs.domainHolderAccount.destroy', $this->addPadToArrayVal([
                    'name'      => $domain[TableEnum::Name->dbName()],
                    'authUser'  => User::authUser()[UsersTableEnum::Username->dbName()],
                    'details'   => json_encode($domain, JSON_PRETTY_PRINT)
                ])),
                'Domain has been deleted'
            );

            $domain->delete();
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
        $this->authorize(PermissionAbilityEnum::viewAny->name, Domain::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);
        return new DomainCollection(
            Domain::ApiIndexCollection($request->input())
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
        $this->authorize(PermissionAbilityEnum::export->name, Domain::class);

        $exporter = new DomainExport($request->all());
        return $exporter->export();
    }
}
