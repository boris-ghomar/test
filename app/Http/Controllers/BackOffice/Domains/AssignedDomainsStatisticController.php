<?php

namespace App\Http\Controllers\BackOffice\Domains;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\AssignedDomainsTableEnum as TableEnum;
use App\Enums\Database\Tables\DomainsTableEnum;
use App\Enums\Domains\DomainStatusEnum;
use App\hhh_Exports\Domains\AssignedDomainStatisticExport;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\jsGrid\jsGrid_Controller;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Resources\BackOffice\Domains\AssignedDomainsStatisticCollection;
use App\Models\BackOffice\Domains\AssignedDomainsStatistic;
use App\Models\User;
use Illuminate\Http\Request;

class AssignedDomainsStatisticController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, AssignedDomainsStatistic::class);

        $jsGrid_Controller = parent::getJsGridType(AssignedDomainsStatistic::class, jsGrid_Controller::jsGridType_Filter);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("domains/assigned_domains_statistics");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'name'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('domain_name', __('general.Domain'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::ClientTrustScore->dbName(), __('thisApp.AdminPages.ClientsManagement.TrustScore'));
        $fieldMaker->makeField_NumberRange();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('domain_status', __("general.Status"));
        $options = DropdownListCreater::makeByArray(DomainStatusEnum::translatedArray())
            ->prepend("", "")->useLable("name", "key")->get();
        $fieldMaker->makeField_Select('key', 'name', "", 'string', $options);
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('clients_count', __('thisApp.AdminPages.Domains.ClientsCount'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_ShowOnly);
        $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('domain_public', __('thisApp.AdminPages.Domains.Public'));
        $fieldMaker->makeField_Checkbox();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('domain_suspicious', __('thisApp.AdminPages.Domains.SuspiciousClients'));
        $fieldMaker->makeField_Checkbox();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('domain_reported', __('thisApp.AdminPages.Domains.Reported'));
        $fieldMaker->makeField_Checkbox();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('ReportsCount', __('thisApp.AdminPages.Domains.ReportsCount'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_ShowOnly);
        $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::FakeAssigned->dbName(), __('thisApp.AdminPages.Domains.FakeAssigned'));
        $fieldMaker->makeField_Checkbox();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(DomainsTableEnum::AnnouncedAt->dbName(), __('thisApp.AdminPages.Domains.announcedAt'));
        $fieldMaker->makeField_DateRange();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "center");
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(DomainsTableEnum::BlockedAt->dbName(), __('thisApp.AdminPages.Domains.blockedAt'));
        $fieldMaker->makeField_DateRange();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "center");
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, null);
        $fieldMaker->makeField_Control();
        $jsGrid_Controller->putField($fieldMaker);


        $data = [
            config('hhh_config.keywords.jsGridJavaData')    =>  $jsGrid_Controller->create(),
            config('hhh_config.keywords.useExcelExport')    => User::authUser()->can(PermissionAbilityEnum::export->name, AssignedDomainsStatistic::class),
        ];


        return view('hhh.BackOffice.pages.Domains.AssignedDomainsStatistics.index', $data);
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     * @param \App\Models\BackOffice\PeronnelManagement\AssignedDomainsStatistic $assignedDomainsStatistic
     */
    public function show(AssignedDomainsStatistic $assignedDomainsStatistic)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param \App\Models\BackOffice\PeronnelManagement\AssignedDomainsStatistic $assignedDomainsStatistic
     */
    public function edit(AssignedDomainsStatistic $assignedDomainsStatistic)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \App\Models\BackOffice\PeronnelManagement\AssignedDomainsStatistic $assignedDomainsStatistic
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(Request $request, AssignedDomainsStatistic $assignedDomainsStatistic)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(Request $request)
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

        $this->authorize(PermissionAbilityEnum::viewAny->name, AssignedDomainsStatistic::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new AssignedDomainsStatisticCollection(
            AssignedDomainsStatistic::ApiIndexCollection($request->input())
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
        $this->authorize(PermissionAbilityEnum::export->name, AssignedDomainsStatistic::class);

        $exporter = new AssignedDomainStatisticExport($request->all());
        return $exporter->export();
    }
}
