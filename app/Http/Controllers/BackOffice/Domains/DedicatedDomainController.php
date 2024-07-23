<?php

namespace App\Http\Controllers\BackOffice\Domains;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\DedicatedDomainsTableEnum as TableEnum;
use App\Enums\Database\Tables\DomainsTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Domains\DomainStatusEnum;
use App\hhh_Exports\Domains\DedicatedDomainExport;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\HHH_Library\ThisApp\Packages\Client\Domain\DomainAssignmentEngine;
use App\Models\BackOffice\Domains\DedicatedDomain;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\Domains\DedicatedDomainRequest;
use App\Http\Resources\BackOffice\Domains\DedicatedDomainCollection;
use App\Http\Resources\BackOffice\Domains\DedicatedDomainResource;
use App\Models\BackOffice\Domains\Domain;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DedicatedDomainController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, DedicatedDomain::class);

        $jsGrid_Controller = parent::getJsGridType(DedicatedDomain::class);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("domains/dedicated_domains");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'name'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $attributes = (new DedicatedDomainRequest())->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Name->dbName(), __('general.Name'));
        $fieldMaker->makeField_Text();
        $attr = $attributes[TableEnum::Name->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('domain_name', __('general.Domain'));
        $fieldMaker->makeField_Text();
        $attr = $attributes[TableEnum::Name->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "left");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('is_blocked', __('thisApp.Enum.DomainStatusEnum.Blocked'));
        $fieldMaker->makeField_Checkbox();
        $fieldMaker->setItemProperties($fieldMaker::field_isInserting, false);
        $fieldMaker->setItemProperties($fieldMaker::field_isFiltering, false);
        $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
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
            config('hhh_config.keywords.useExcelExport')    => User::authUser()->can(PermissionAbilityEnum::export->name, DedicatedDomain::class),
        ];

        return view('hhh.BackOffice.pages.Domains.DedicatedDomains.index', $data);
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
     * @param  \App\Http\Requests\BackOffice\Domains\DedicatedDomainRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function store(DedicatedDomainRequest $request): JsonResponse
    {
        try {

            $dedicatedDomain = new DedicatedDomain();

            $dedicatedDomain->fill($request->all());

            $domain = $this->fetchFreshDomain($dedicatedDomain);
            if (is_null($domain))
                return JsonResponseHelper::errorResponse('thisApp.Errors.DedicatedDomains.DomainNotFound', __('thisApp.Errors.DedicatedDomains.DomainNotFound'), HttpResponseStatusCode::NotFound->value);

            $dedicatedDomain[TableEnum::DomainId->dbName()] = $domain[DomainsTableEnum::Id->dbName()];
            $dedicatedDomain->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new DedicatedDomainResource($dedicatedDomain), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Display the specified resource.
     * @param \App\Models\BackOffice\Domains\DedicatedDomain $dedicatedDomain
     */
    public function show(DedicatedDomain $dedicatedDomain)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param \App\Models\BackOffice\Domains\DedicatedDomain $dedicatedDomain
     */
    public function edit(DedicatedDomain $dedicatedDomain)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\Domains\DedicatedDomainRequest $request
     * @param \App\Models\BackOffice\Domains\DedicatedDomain $dedicatedDomain
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(DedicatedDomainRequest $request, DedicatedDomain $dedicatedDomain): JsonResponse
    {
        try {

            $dedicatedDomain = DedicatedDomain::find($request->input(TableEnum::Id->dbName()));
            $dedicatedDomain->fill($request->all());
            $dedicatedDomain->save();

            if ($request->input('is_blocked')) {

                // Update blocked domain
                $lastDomain = $dedicatedDomain->domain;
                $lastDomain[DomainsTableEnum::Status->dbName()] = DomainStatusEnum::Blocked->name;
                $lastDomain[DomainsTableEnum::BlockedAt->dbName()] = Carbon::now();
                $lastDomain->save();

                $dedicatedDomain = DedicatedDomain::find($request->input(TableEnum::Id->dbName()));

                $domain = $this->fetchFreshDomain($dedicatedDomain);

                if (is_null($domain))
                    return JsonResponseHelper::errorResponse('thisApp.Errors.DedicatedDomains.DomainNotFound', __('thisApp.Errors.DedicatedDomains.DomainNotFound'), HttpResponseStatusCode::NotFound->value);

                $dedicatedDomain[TableEnum::DomainId->dbName()] = $domain[DomainsTableEnum::Id->dbName()];
                $dedicatedDomain->save();
            }
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new DedicatedDomainResource($dedicatedDomain), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\Domains\DedicatedDomainRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(DedicatedDomainRequest $request): JsonResponse
    {
        if ($dedicatedDomain = DedicatedDomain::find($request->input(TableEnum::Id->dbName()))) {

            $domain = $dedicatedDomain->domain;

            // Return domain to useable status
            $domain[DomainsTableEnum::Status->dbName()] = DomainStatusEnum::ReadyToUse->name;
            $domain[DomainsTableEnum::AnnouncedAt->dbName()] = null;
            $domain[DomainsTableEnum::Descr->dbName()] = null;
            $domain->save();

            $dedicatedDomain->delete();
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

        $this->authorize(PermissionAbilityEnum::viewAny->name, DedicatedDomain::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new DedicatedDomainCollection(
            DedicatedDomain::ApiIndexCollection($request->input())
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
        $this->authorize(PermissionAbilityEnum::export->name, DedicatedDomain::class);

        $exporter = new DedicatedDomainExport($request->all());
        return $exporter->export();
    }

    /**
     * Fetch fresh domain to assign
     *
     * @param  \App\Models\BackOffice\Domains\DedicatedDomain $dedicatedDomain
     * @return null|\App\Models\BackOffice\Domains\Domain
     */
    private function fetchFreshDomain(DedicatedDomain $dedicatedDomain): ?Domain
    {
        $domain = DomainAssignmentEngine::fetchFreshDomain();

        if (!is_null($domain)) {

            $domain[DomainsTableEnum::Status->dbName()] = DomainStatusEnum::InUse->name;
            $domain[DomainsTableEnum::AnnouncedAt->dbName()] = Carbon::now();
            $domain[DomainsTableEnum::Descr->dbName()] = sprintf(
                'This domain was assigned to "%s" by %s.',
                $dedicatedDomain[TableEnum::Name->dbName()],
                auth()->user()[UsersTableEnum::Username->dbName()],
            );

            $domain->save();

            return $domain;
        }

        return null;
    }
}
