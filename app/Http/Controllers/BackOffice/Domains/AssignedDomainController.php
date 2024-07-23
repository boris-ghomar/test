<?php

namespace App\Http\Controllers\BackOffice\Domains;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\AssignedDomainsTableEnum as TableEnum;
use App\Enums\Database\Tables\DomainsTableEnum;
use App\Enums\Domains\DomainStatusEnum;
use App\hhh_Exports\Domains\AssignedDomainExport;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\jsGrid\jsGrid_Controller;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Models\BackOffice\Domains\AssignedDomain;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Resources\BackOffice\Domains\AssignedDomainCollection;
use App\Models\User;
use Illuminate\Http\Request;

class AssignedDomainController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, AssignedDomain::class);

        $jsGrid_Controller = parent::getJsGridType(AssignedDomain::class, jsGrid_Controller::jsGridType_Filter);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("domains/assigned_domains");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'name'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::UserId->dbName(), __('thisApp.UserId'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('username', __('general.UserName'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('domain_name', __('general.Domain'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::ClientTrustScore->dbName(), __('thisApp.AdminPages.Domains.ClientTrustScoreAssignment'));
        $fieldMaker->makeField_NumberRange();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('current_trust_score', __('thisApp.AdminPages.Domains.ClientTrustScoreCurrent'));
        $fieldMaker->makeField_NumberRange();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::DomainSuspiciousScore->dbName(), __('thisApp.AdminPages.Domains.DomainSuspiciousAssignment'));
        $fieldMaker->makeField_NumberRange();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('current_domain_suspicious_score', __('thisApp.AdminPages.Domains.DomainSuspiciousCurrent'));
        $fieldMaker->makeField_NumberRange();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('domain_status', __("general.Status"));
        $options = DropdownListCreater::makeByArray(DomainStatusEnum::translatedArray())
            ->prepend("", "")->useLable("name", "key")->get();
        $fieldMaker->makeField_Select('key', 'name', "", 'string', $options);
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(DomainsTableEnum::Public->dbName(), __('thisApp.AdminPages.Domains.Public'));
        $fieldMaker->makeField_Checkbox();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(DomainsTableEnum::Suspicious->dbName(), __('thisApp.AdminPages.Domains.SuspiciousClients'));
        $fieldMaker->makeField_Checkbox();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Reported->dbName(), __('thisApp.AdminPages.Domains.Reported'));
        $fieldMaker->makeField_Checkbox();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
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

        $fieldMaker = new jsGrid_FieldMaker(TimestampsEnum::CreatedAt->dbName(), __('thisApp.AdminPages.Domains.assignedAt'));
        $fieldMaker->makeField_DateRange();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "center");
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::ReportedAt->dbName(), __('thisApp.AdminPages.Domains.reportedAt'));
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
            config('hhh_config.keywords.useExcelExport')    => User::authUser()->can(PermissionAbilityEnum::export->name, AssignedDomain::class),
        ];


        return view('hhh.BackOffice.pages.Domains.AssignedDomains.index', $data);
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
     * @param \App\Models\BackOffice\PeronnelManagement\AssignedDomain $assignedDomain
     */
    public function show(AssignedDomain $assignedDomain)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param \App\Models\BackOffice\PeronnelManagement\AssignedDomain $assignedDomain
     */
    public function edit(AssignedDomain $assignedDomain)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \App\Models\BackOffice\PeronnelManagement\AssignedDomain $assignedDomain
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(Request $request, AssignedDomain $assignedDomain)
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

        $this->authorize(PermissionAbilityEnum::viewAny->name, AssignedDomain::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new AssignedDomainCollection(
            AssignedDomain::ApiIndexCollection($request->input())
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
        $this->authorize(PermissionAbilityEnum::export->name, AssignedDomain::class);

        $exporter = new AssignedDomainExport($request->all());
        return $exporter->export();
    }
}
