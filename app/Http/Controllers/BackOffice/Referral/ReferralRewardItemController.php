<?php

namespace App\Http\Controllers\BackOffice\Referral;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\ReferralRewardItemsTableEnum as TableEnum;
use App\Enums\Database\Tables\ReferralRewardPackagesTableEnum;
use App\Enums\Referral\ReferralRewardTypeEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Models\BackOffice\Referral\ReferralRewardItem;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\Referral\ReferralRewardItemRequest;
use App\Http\Resources\BackOffice\Referral\ReferralRewardItemCollection;
use App\Http\Resources\BackOffice\Referral\ReferralRewardItemResource;
use App\Models\BackOffice\Referral\ReferralRewardPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralRewardItemController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, ReferralRewardItem::class);

        $jsGrid_Controller = parent::getJsGridType(ReferralRewardItem::class);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("referral/referral_reward_items");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'name'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $attributes = (new ReferralRewardItemRequest())->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::PackageId->dbName(), __("thisApp.AdminPages.Referral.RewardPackage"));
        $activeReferralRewardPackagesQuery = ReferralRewardPackage::where(ReferralRewardPackagesTableEnum::IsActive->dbName(), 1);
        $options = DropdownListCreater::makeByModelQuery($activeReferralRewardPackagesQuery, ReferralRewardPackagesTableEnum::Name->dbName())
            ->prepend("", -1)->useLable("name", "id")->sort(true)->get();
        $fieldMaker->makeField_Select('id', 'name', -1, 'number', $options);
        $attr = $attributes[TableEnum::PackageId->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value > 0;}", trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

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

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Type->dbName(), __('thisApp.AdminPages.Referral.RewardType'));
        $options = DropdownListCreater::makeByArray(ReferralRewardTypeEnum::translatedArray())
            ->useLable("name", "key")
            ->prepend("", "")
            ->sort(true)->get();
        $fieldMaker->makeField_Select('key', 'name', "", 'string', $options);
        $attr = $attributes[TableEnum::Type->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value != '';}", trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::BonusId->dbName(), __("thisApp.AdminPages.Referral.BonusId"), __('thisApp.AdminPages.Referral.BonusIdRequiredDescr'));
        $fieldMaker->makeField_Text();
        $attr = $attributes[TableEnum::BonusId->dbName()];
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "140");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Percentage->dbName(), __("thisApp.AdminPages.Referral.RewardPercentage"));
        $fieldMaker->makeField_NumberRange();
        $attr = $attributes[TableEnum::Percentage->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "140");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::IsActive->dbName(), __('general.isActive'));
        $fieldMaker->makeField_Checkbox();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::DisplayPriority->dbName(), __('thisApp.DisplayPriority'));
        $fieldMaker->makeField_Number();
        $attr = $attributes[TableEnum::DisplayPriority->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->addValidate($fieldMaker::validator_min, 1, trans('validation.min.numeric', ['attribute' => $attr, 'min' => 1]));
        $fieldMaker->setItemProperties($fieldMaker::field_Width, '100');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_isInserting, false);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::PaymentPriority->dbName(), __('thisApp.PaymentPriority'));
        $fieldMaker->makeField_Number();
        $attr = $attributes[TableEnum::PaymentPriority->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->addValidate($fieldMaker::validator_min, 1, trans('validation.min.numeric', ['attribute' => $attr, 'min' => 1]));
        $fieldMaker->setItemProperties($fieldMaker::field_Width, '100');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_isInserting, false);
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


        return view('hhh.BackOffice.pages.Referral.RewardItems.index', $data);
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
     * @param  \App\Http\Requests\BackOffice\Referral\ReferralRewardItemRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function store(ReferralRewardItemRequest $request): JsonResponse
    {
        try {

            $item = new ReferralRewardItem();

            $item->fill($request->all());

            $packageIdCol = TableEnum::PackageId->dbName();

            $priorityCol = TableEnum::DisplayPriority->dbName();
            $lastPosition = ReferralRewardItem::select($priorityCol)->where($packageIdCol, $item[$packageIdCol])->orderBy($priorityCol, 'desc')->first();
            $item->$priorityCol = is_null($lastPosition) ? 1 : $lastPosition->$priorityCol + 1;

            $priorityCol = TableEnum::PaymentPriority->dbName();
            $lastPosition = ReferralRewardItem::select($priorityCol)->where($packageIdCol, $item[$packageIdCol])->orderBy($priorityCol, 'desc')->first();
            $item->$priorityCol = is_null($lastPosition) ? 1 : $lastPosition->$priorityCol + 1;

            $item->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new ReferralRewardItemResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Display the specified resource.
     * @param \App\Models\BackOffice\Referral\ReferralRewardItem $referralRewardItem
     */
    public function show(ReferralRewardItem $referralRewardItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param \App\Models\BackOffice\Referral\ReferralRewardItem $referralRewardItem
     */
    public function edit(ReferralRewardItem $referralRewardItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\Referral\ReferralRewardItemRequest $request
     * @param \App\Models\BackOffice\Referral\ReferralRewardItem $referralRewardItem
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(ReferralRewardItemRequest $request, ReferralRewardItem $referralRewardItem): JsonResponse
    {
        try {

            $item = ReferralRewardItem::find($request->input(TableEnum::Id->dbName()));

            $item->fill($request->all());

            $displayPriorityCol = TableEnum::DisplayPriority->dbName();
            if ($isDirtyDisplayPriorityCol = $item->isDirty($displayPriorityCol))
                $lastDisplayPriority = $item->getOriginal($displayPriorityCol);

            $paymentPriorityCol = TableEnum::PaymentPriority->dbName();
            if ($isDirtyPaymentPriorityCol = $item->isDirty($paymentPriorityCol))
                $lastPaymentPriority = $item->getOriginal($paymentPriorityCol);

            $item->save();

            if ($isDirtyDisplayPriorityCol)
                $this->adjustPriorities($displayPriorityCol, $item, $lastDisplayPriority);

            if ($isDirtyPaymentPriorityCol)
                $this->adjustPriorities($paymentPriorityCol, $item, $lastPaymentPriority);

            $item->refresh();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new ReferralRewardItemResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\Referral\ReferralRewardItemRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(ReferralRewardItemRequest $request): JsonResponse
    {
        if ($item = ReferralRewardItem::find($request->input(TableEnum::Id->dbName()))) {

            $this->adjustPriorities(TableEnum::DisplayPriority->dbName(), $item, -1);
            $this->adjustPriorities(TableEnum::PaymentPriority->dbName(), $item, -1);

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

        $this->authorize(PermissionAbilityEnum::viewAny->name, ReferralRewardItem::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new ReferralRewardItemCollection(
            ReferralRewardItem::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }

    /**
     * Adjust items Priorities
     *
     * @param  string $priorityCol
     * @param \App\Models\BackOffice\Referral\ReferralRewardItem $referralRewardItem
     * @param  int $lastPriority
     * @return void
     */
    private function adjustPriorities(string $priorityCol, ReferralRewardItem $referralRewardItem, int $lastPriority): void
    {
        $idCol = TableEnum::Id->dbName();
        $packageIdCol = TableEnum::PackageId->dbName();

        $currentPriority = $referralRewardItem->$priorityCol;

        $updatedAtSort = ($currentPriority < $lastPriority) ? "desc" : "asc";

        $itemsQuery = ReferralRewardItem::select($idCol, $priorityCol)
            ->where($packageIdCol, $referralRewardItem->$packageIdCol)
            ->orderBy($priorityCol, 'asc')
            ->orderBy(TimestampsEnum::UpdatedAt->dbName(), $updatedAtSort);

        if ($lastPriority < 0) {
            // Item is deleting

            $itemsQuery->where($idCol, '!=', $referralRewardItem->$idCol);
        }

        $items = $itemsQuery->get();

        $priority = 1;
        foreach ($items as $item) {

            $item->$priorityCol = $priority;
            $item->save();

            $priority++;
        }
    }
}
