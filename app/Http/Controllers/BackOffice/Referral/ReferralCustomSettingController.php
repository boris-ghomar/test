<?php

namespace App\Http\Controllers\BackOffice\Referral;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\ReferralCustomSettingsTableEnum as TableEnum;
use App\Enums\Database\Tables\ReferralRewardPackagesTableEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Models\BackOffice\Referral\ReferralCustomSetting;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\Referral\ReferralCustomSettingRequest;
use App\Http\Resources\BackOffice\Referral\ReferralCustomSettingCollection;
use App\Http\Resources\BackOffice\Referral\ReferralCustomSettingResource;
use App\Models\BackOffice\Referral\ReferralRewardPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralCustomSettingController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, ReferralCustomSetting::class);

        $jsGrid_Controller = parent::getJsGridType(ReferralCustomSetting::class);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("referral/client_custom_settings");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'name'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $attributes = (new ReferralCustomSettingRequest())->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::UserId->dbName(), __("thisApp.UserId"));
        $fieldMaker->makeField_Number();
        $attr = $attributes[TableEnum::UserId->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "140");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('bc_username', __("general.UserName"));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "left");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('bc_id', __("thisApp.BetconstructId"));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "left");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::PackageId->dbName(), __("thisApp.AdminPages.Referral.RewardPackage"));
        $activeReferralRewardPackagesQuery = ReferralRewardPackage::where(ReferralRewardPackagesTableEnum::IsActive->dbName(), 1);
        $options = DropdownListCreater::makeByModelQuery($activeReferralRewardPackagesQuery, ReferralRewardPackagesTableEnum::Name->dbName())
            ->prepend("", -1)->useLable("name", "id")->sort(true)->get();
        $fieldMaker->makeField_Select('id', 'name', -1, 'number', $options);
        $attr = $attributes[TableEnum::PackageId->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value > 0;}", trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
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


        return view('hhh.BackOffice.pages.Referral.ClientCustomSettings.index', $data);
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
     * @param \App\Http\Requests\BackOffice\Referral\ReferralCustomSettingRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function store(ReferralCustomSettingRequest $request): JsonResponse
    {
        try {

            $item = new ReferralCustomSetting();

            $item->fill($request->all());
            $item->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new ReferralCustomSettingResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Display the specified resource.
     * @param \App\Models\BackOffice\Referral\ReferralCustomSetting $referralCustomSetting
     */
    public function show(ReferralCustomSetting $referralCustomSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param \App\Models\BackOffice\Referral\ReferralCustomSetting $referralCustomSetting
     */
    public function edit(ReferralCustomSetting $referralCustomSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\BackOffice\Referral\ReferralCustomSettingRequest $request $request
     * @param \App\Models\BackOffice\Referral\ReferralCustomSetting $referralCustomSetting
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(ReferralCustomSettingRequest $request, ReferralCustomSetting $referralCustomSetting): JsonResponse
    {
        try {

            $item = ReferralCustomSetting::find($request->input(TableEnum::Id->dbName()));

            $item->fill($request->all());
            $item->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new ReferralCustomSettingResource($request->all()), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\BackOffice\Referral\ReferralCustomSettingRequest $request $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(ReferralCustomSettingRequest $request): JsonResponse
    {
        if ($item = ReferralCustomSetting::find($request->input(TableEnum::Id->dbName()))) {

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

        $this->authorize(PermissionAbilityEnum::viewAny->name, ReferralCustomSetting::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new ReferralCustomSettingCollection(
            ReferralCustomSetting::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }
}
