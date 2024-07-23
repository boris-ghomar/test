<?php

namespace App\Http\Controllers\BackOffice\Referral;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\ReferralRewardPackagesTableEnum as TableEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Models\BackOffice\Referral\ReferralRewardPackage;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\Referral\ReferralRewardPackageRequest;
use App\Http\Resources\BackOffice\Referral\ReferralRewardPackageCollection;
use App\Http\Resources\BackOffice\Referral\ReferralRewardPackageResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralRewardPackageController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, ReferralRewardPackage::class);

        $jsGrid_Controller = parent::getJsGridType(ReferralRewardPackage::class);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("referral/referral_reward_packages");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'name'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $attributes = (new ReferralRewardPackageRequest())->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Name->dbName(), __('general.Name'));
        $fieldMaker->makeField_Text();
        $attr = $attributes[TableEnum::Name->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::DisplayName->dbName(), __('thisApp.DisplayName'));
        $fieldMaker->makeField_Text();
        $attr = $attributes[TableEnum::DisplayName->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::ClaimCount->dbName(), __('thisApp.AdminPages.Referral.ClaimCount'));
        $fieldMaker->makeField_NumberRange();
        $attr = $attributes[TableEnum::ClaimCount->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "center");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Descr->dbName(), __('general.Description'));
        $fieldMaker->makeField_Textarea();
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "300");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::IsActive->dbName(), __('general.isActive'));
        $fieldMaker->makeField_Checkbox();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::MinBetCountReferrer->dbName(), __('thisApp.AdminPages.Referral.MinBetCount'), __('thisApp.AdminPages.Referral.subTitle.Referrer'));
        $fieldMaker->makeField_NumberRange();
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::MinBetOddsReferrer->dbName(), __('thisApp.AdminPages.Referral.MinBetOdds'), __('thisApp.AdminPages.Referral.subTitle.Referrer'));
        $fieldMaker->makeField_NumberRange();
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::MinBetAmountUsdReferrer->dbName(), __('thisApp.AdminPages.Referral.MinBetAmount'), __('thisApp.AdminPages.Referral.subTitle.ReferrerUsd'));
        $fieldMaker->makeField_NumberRange();
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::MinBetAmountIrrReferrer->dbName(), __('thisApp.AdminPages.Referral.MinBetAmount'), __('thisApp.AdminPages.Referral.subTitle.ReferrerIrr'));
        $fieldMaker->makeField_NumberRange();
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::MinBetCountReferred->dbName(), __('thisApp.AdminPages.Referral.MinBetCount'), __('thisApp.AdminPages.Referral.subTitle.Referred'));
        $fieldMaker->makeField_NumberRange();
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::MinBetOddsReferred->dbName(), __('thisApp.AdminPages.Referral.MinBetOdds'), __('thisApp.AdminPages.Referral.subTitle.Referred'));
        $fieldMaker->makeField_NumberRange();
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::MinBetAmountUsdReferred->dbName(), __('thisApp.AdminPages.Referral.MinBetAmount'), __('thisApp.AdminPages.Referral.subTitle.ReferredUsd'));
        $fieldMaker->makeField_NumberRange();
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::MinBetAmountIrrReferred->dbName(), __('thisApp.AdminPages.Referral.MinBetAmount'), __('thisApp.AdminPages.Referral.subTitle.ReferredIrr'));
        $fieldMaker->makeField_NumberRange();
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::PrivateNote->dbName(), __('thisApp.PrivateNote'));
        $fieldMaker->makeField_Textarea();
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "300");
        $jsGrid_Controller->putField($fieldMaker);


        $fieldMaker = new jsGrid_FieldMaker(null, null);
        $fieldMaker->makeField_Control();
        $jsGrid_Controller->putField($fieldMaker);


        $data = [
            config('hhh_config.keywords.jsGridJavaData')    =>  $jsGrid_Controller->create(),
        ];


        return view('hhh.BackOffice.pages.Referral.RewardPackages.index', $data);
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
     * @param  \App\Http\Requests\BackOffice\Referral\ReferralRewardPackageRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function store(ReferralRewardPackageRequest $request): JsonResponse
    {
        try {

            $item = new ReferralRewardPackage();

            $item->fill($request->all());
            $item->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new ReferralRewardPackageResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Display the specified resource.
     * @param \App\Models\BackOffice\Referral\ReferralRewardPackage $referralRewardPackage
     */
    public function show(ReferralRewardPackage $referralRewardPackage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param \App\Models\BackOffice\Referral\ReferralRewardPackage $referralRewardPackage
     */
    public function edit(ReferralRewardPackage $referralRewardPackage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\Referral\ReferralRewardPackageRequest $request
     * @param \App\Models\BackOffice\Referral\ReferralRewardPackage $referralRewardPackage
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(ReferralRewardPackageRequest $request, ReferralRewardPackage $referralRewardPackage): JsonResponse
    {
        try {

            $item = ReferralRewardPackage::find($request->input(TableEnum::Id->dbName()));

            $item->fill($request->all());
            $item->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new ReferralRewardPackageResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\Referral\ReferralRewardPackageRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(ReferralRewardPackageRequest $request): JsonResponse
    {
        if ($item = ReferralRewardPackage::find($request->input(TableEnum::Id->dbName()))) {

            $item->delete();
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

        $this->authorize(PermissionAbilityEnum::viewAny->name, ReferralRewardPackage::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new ReferralRewardPackageCollection(
            ReferralRewardPackage::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }
}
