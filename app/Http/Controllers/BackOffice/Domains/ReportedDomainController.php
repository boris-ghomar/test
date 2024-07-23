<?php

namespace App\Http\Controllers\BackOffice\Domains;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\DomainsTableEnum as TableEnum;
use App\Enums\Domains\DomainStatusEnum;
use App\hhh_Exports\Domains\ReportedDomainExport;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_Controller;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\HHH_Library\ThisApp\Packages\Client\TrustScore\ClientTrustScoreEngine;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\Domains\ReportedDomainRequest;
use App\Http\Resources\BackOffice\Domains\ReportedDomainCollection;
use App\Models\BackOffice\Domains\ReportedDomain;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportedDomainController extends SuperJsGridController
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, ReportedDomain::class);

        $jsGrid_Controller = parent::getJsGridType(ReportedDomain::class, jsGrid_Controller::jsGridType_Edit);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("domains/reported_domains");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'name'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Name->dbName(), __('general.Name'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('ReportsCount', __('thisApp.AdminPages.Domains.ReportsCount'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_ShowOnly);
        $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $attributes = (new ReportedDomainRequest())->attributes();

        $fieldMaker = new jsGrid_FieldMaker('review', __("general.Status"));
        $options = DropdownListCreater::makeByArray(__('thisApp.ReportedDomainReview'))
            ->prepend("", "")->useLable("key", "name")->get();
        $fieldMaker->makeField_Select('key', 'name', "", 'string', $options);
        $attr = $attributes['review'];
        $fieldMaker->setItemProperties($fieldMaker::field_isFiltering, false);
        $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
        $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value != '';}", trans('validation.required', ['attribute' => $attr]));
        $jsGrid_Controller->putField($fieldMaker);


        $fieldMaker = new jsGrid_FieldMaker(null, null);
        $fieldMaker->makeField_Control();
        $jsGrid_Controller->putField($fieldMaker);


        $data = [
            config('hhh_config.keywords.jsGridJavaData')    =>  $jsGrid_Controller->create(),
            config('hhh_config.keywords.useExcelExport')    => User::authUser()->can(PermissionAbilityEnum::export->name, ReportedDomain::class),
        ];


        return view('hhh.BackOffice.pages.Domains.ReportedDomains.index', $data);
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
     * @param  \App\Http\Requests\BackOffice\Domains\ReportedDomainRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     * @param \App\Models\BackOffice\Domains\ReportedDomain $reportedDomain
     */
    public function show(ReportedDomain $reportedDomain)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param \App\Models\BackOffice\Domains\ReportedDomain $reportedDomain
     */
    public function edit(ReportedDomain $reportedDomain)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\Domains\ReportedDomainRequest $request
     * @param \App\Models\BackOffice\Domains\ReportedDomain $reportedDomain
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(ReportedDomainRequest $request, ReportedDomain $reportedDomain): JsonResponse
    {
        try {
            if ($reportedDomain = ReportedDomain::find($request->input(TableEnum::Id->dbName()))) {

                $statusCol = TableEnum::Status->dbName();
                $reportedCol = TableEnum::Reported->dbName();

                $review = $request->input('review');

                if ($review == "blocked") {
                    // Domain is blocked

                    $reportedDomain->$statusCol = DomainStatusEnum::Blocked->name;

                    // ClientTrustScoreEngine::domainBlocked($reportedDomain); // No need: this item will be handle in domain model boot function

                } else {
                    // Domain is working without problem

                    $reportedDomain->$reportedCol = 0;
                    ClientTrustScoreEngine::domainReportedFake($reportedDomain);
                }

                $reportedDomain->save();
            } else
                return JsonResponseHelper::errorResponse('general.NotFoundItem', __('general.NotFoundItem'), HttpResponseStatusCode::NotFound->value);
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse($request->all(), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\Domains\ReportedDomainRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(ReportedDomainRequest $request)
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

        $this->authorize(PermissionAbilityEnum::viewAny->name, ReportedDomain::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new ReportedDomainCollection(
            ReportedDomain::ApiIndexCollection($request->input())
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
        $this->authorize(PermissionAbilityEnum::export->name, ReportedDomain::class);

        $exporter = new ReportedDomainExport($request->all());
        return $exporter->export();
    }
}
