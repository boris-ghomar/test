<?php

namespace App\Http\Controllers\BackOffice\Referral;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\ReferralsTableEnum as TableEnum;
use App\hhh_Exports\Referral\ReferralExport;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_Controller;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\Referral\ReferralRequest;
use App\Http\Resources\BackOffice\Referral\ReferralCollection;
use App\Http\Resources\BackOffice\Referral\ReferralResource;
use App\Models\BackOffice\Referral\Referral;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, Referral::class);

        $jsGrid_Controller = parent::getJsGridType(Referral::class, jsGrid_Controller::jsGridType_Edit);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("referral/referrals_management");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'name'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $attributes = (new ReferralRequest())->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::UserId->dbName(), __('thisApp.UserId'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('referred_bc_id', __('thisApp.BetconstructId'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('referred_username', __('general.UserName'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::ReferredBy->dbName(), __('thisApp.UserId'), __('thisApp.AdminPages.Referral.Referrer'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('referrer_bc_id', __('thisApp.BetconstructId'), __('thisApp.AdminPages.Referral.Referrer'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('referrer_username', __('general.UserName'), __('thisApp.AdminPages.Referral.Referrer'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TimestampsEnum::CreatedAt->dbName(), __('general.CreatedAt'));
        $fieldMaker->makeField_DateRange();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $jsGrid_Controller->putField($fieldMaker);


        $fieldMaker = new jsGrid_FieldMaker(null, null);
        $fieldMaker->makeField_Control();
        $jsGrid_Controller->putField($fieldMaker);


        $data = [
            config('hhh_config.keywords.jsGridJavaData')    =>  $jsGrid_Controller->create(),
            config('hhh_config.keywords.useExcelExport')    => User::authUser()->can(PermissionAbilityEnum::export->name, Referral::class),
        ];


        return view('hhh.BackOffice.pages.Referral.ReferralsManagement.index', $data);
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
     * @param  \App\Http\Requests\BackOffice\Referral\ReferralRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function store(ReferralRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     * @param \App\Models\BackOffice\Referral\Referral $referral
     */
    public function show(Referral $referral)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param \App\Models\BackOffice\Referral\Referral $referral
     */
    public function edit(Referral $referral)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\Referral\ReferralRequest $request
     * @param \App\Models\BackOffice\Referral\Referral $referral
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(ReferralRequest $request, Referral $referral): JsonResponse
    {
        try {

            $item = Referral::find($request->input(TableEnum::Id->dbName()));

            $item->fill($request->only(TableEnum::ReferredBy->dbName()));
            $item->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        $userId = $request->input(TableEnum::UserId->dbName());
        $resource = Referral::ApiIndexCollection([TableEnum::UserId->dbName() => $userId])->first();
        return JsonResponseHelper::successResponse(new ReferralResource($resource), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\Referral\ReferralRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(ReferralRequest $request)
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

        $this->authorize(PermissionAbilityEnum::viewAny->name, Referral::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new ReferralCollection(
            Referral::ApiIndexCollection($request->input())
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
        $this->authorize(PermissionAbilityEnum::export->name, Referral::class);

        $exporter = new ReferralExport($request->all());
        return $exporter->export();
    }
}
