<?php

namespace App\Http\Controllers\BackOffice\Referral;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\ReferralRewardPackagesTableEnum;
use App\Enums\Database\Tables\ReferralSessionsTableEnum as TableEnum;
use App\Enums\Referral\ReferralSessionStatusEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Models\BackOffice\Referral\ReferralSession;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\Referral\ReferralSessionRequest;
use App\Http\Resources\BackOffice\Referral\ReferralSessionCollection;
use App\Http\Resources\BackOffice\Referral\ReferralSessionResource;
use App\Models\BackOffice\Referral\ReferralRewardPackage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralSessionController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, ReferralSession::class);

        $jsGrid_Controller = parent::getJsGridType(ReferralSession::class);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("referral/referral_sessions");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'name'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $attributes = (new ReferralSessionRequest())->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Name->dbName(), __('general.Name'));
        $fieldMaker->makeField_Text();
        $attr = $attributes[TableEnum::Name->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
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

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Status->dbName(), __("general.Status"));
        $options = DropdownListCreater::makeByArray(ReferralSessionStatusEnum::translatedArray())
            ->useLable("name", "key")->prepend("", "")->get();
        $fieldMaker->makeField_Select('key', 'name', "", 'string', $options);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "start");
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::StartedAt->dbName(), __('thisApp.StartTime'));
        $fieldMaker->makeField_DateRange();
        $attr = $attributes[TableEnum::StartedAt->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::FinishedAt->dbName(), __('thisApp.FinishTime'));
        $fieldMaker->makeField_DateRange();
        $attr = $attributes[TableEnum::FinishedAt->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
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


        return view('hhh.BackOffice.pages.Referral.ReferralSessions.index', $data);
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
     * @param  \App\Http\Requests\BackOffice\Referral\ReferralSessionRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function store(ReferralSessionRequest $request): JsonResponse
    {
        try {

            $item = new ReferralSession();

            $item->fill($request->all());

            $validateDates = $this->validateDates($item);
            if ($validateDates !== true)
                return JsonResponseHelper::errorResponse(null, $validateDates, HttpResponseStatusCode::BadRequest->value);

            $item->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new ReferralSessionResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Display the specified resource.
     * @param \App\Models\BackOffice\Referral\ReferralSession $referralSession
     */
    public function show(ReferralSession $referralSession)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param \App\Models\BackOffice\Referral\ReferralSession $referralSession
     */
    public function edit(ReferralSession $referralSession)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\Referral\ReferralSessionRequest $request
     * @param \App\Models\BackOffice\Referral\ReferralSession $referralSession
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(ReferralSessionRequest $request, ReferralSession $referralSession): JsonResponse
    {
        try {

            $item = ReferralSession::find($request->input(TableEnum::Id->dbName()));

            $item->fill($request->all());

            $validation = $this->validateUpdate($item);
            if ($validation !== true)
                return JsonResponseHelper::errorResponse(null, $validation, HttpResponseStatusCode::BadRequest->value);

            $validation = $this->validateDates($item);
            if ($validation !== true)
                return JsonResponseHelper::errorResponse(null, $validation, HttpResponseStatusCode::BadRequest->value);


            $item->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new ReferralSessionResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\Referral\ReferralSessionRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(ReferralSessionRequest $request): JsonResponse
    {
        if ($item = ReferralSession::find($request->input(TableEnum::Id->dbName()))) {

            $status = $item->getAttribute(TableEnum::Status->dbName());

            $alowedDestroyStatus = ReferralSessionStatusEnum::getAllowedToDelete(true);
            if (!in_array($status, $alowedDestroyStatus))
                return JsonResponseHelper::errorResponse(null, trans('thisApp.Errors.Referral.ReferralSessionDestroyError', ['name' => $item[TableEnum::Name->dbName()]]), HttpResponseStatusCode::Forbidden->value);


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

        $this->authorize(PermissionAbilityEnum::viewAny->name, ReferralSession::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new ReferralSessionCollection(
            ReferralSession::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }

    /**
     * Validate dates
     *
     * @param \App\Models\BackOffice\Referral\ReferralSession $referralSession
     * @return bool|string true: success | string: error message
     */
    private function validateDates(ReferralSession $referralSession): bool|string
    {
        $id = $referralSession->getAttribute(TableEnum::Id->dbName());
        $startDate = $referralSession->getAttribute(TableEnum::StartedAt->dbName());
        $finishDate = $referralSession->getAttribute(TableEnum::FinishedAt->dbName());

        $isUpdating = !empty($id);
        $checkMinDates = true;

        if ($isUpdating) {

            // If updating and dates are not dirty, ignore minimum dates
            if (!$referralSession->isDirty([TableEnum::StartedAt->dbName(), TableEnum::FinishedAt->dbName()])) {
                $checkMinDates = false;
            }
        }

        $user = User::authUser();
        $startDateUTC = Carbon::parse($user->convertLocalTimeToUTC($startDate));
        $finishDateUTC = Carbon::parse($user->convertLocalTimeToUTC($finishDate));

        // Check minimum of dates
        if ($checkMinDates) {

            // Check min start date
            $minStartDateUTC = now()->addHours(3);

            if ($startDateUTC < $minStartDateUTC->subSecond())
                return __('validation.gte.numeric', ['attribute' => __('thisApp.StartTime'), 'value' => $user->convertUTCToLocalTime($minStartDateUTC->toDateTimeString())]);

            // Check min finish date
            $minFinishDateUTC = max(Carbon::parse($startDateUTC)->addDay(), now()->addDay());

            if ($finishDateUTC < $minFinishDateUTC->subSecond())
                return __('validation.gte.numeric', ['attribute' => __('thisApp.FinishTime'), 'value' => $user->convertUTCToLocalTime($minFinishDateUTC->toDateTimeString())]);
        }


        /********************** Check date conflict **********************/

        // Check if start date falls within another session date range or not
        $referralSessionConflict = ReferralSession::where(TableEnum::Id->dbName(), '!=', $id)
            ->where(TableEnum::StartedAt->dbName(), '<=', $startDateUTC->toDateTimeString())
            ->where(TableEnum::FinishedAt->dbName(), '>=', $startDateUTC->toDateTimeString())
            ->first();

        if (!is_null($referralSessionConflict))
            return __('thisApp.Errors.Referral.ReferralSessionStartDateConflict', ['name' => $referralSessionConflict[TableEnum::Name->dbName()]]);

        // Check if finish date falls within another session date range or not
        $referralSessionConflict = ReferralSession::where(TableEnum::Id->dbName(), '!=', $id)
            ->where(TableEnum::StartedAt->dbName(), '<=', $finishDateUTC->toDateTimeString())
            ->where(TableEnum::FinishedAt->dbName(), '>=', $finishDateUTC->toDateTimeString())
            ->first();

        if (!is_null($referralSessionConflict))
            return __('thisApp.Errors.Referral.ReferralSessionFinishDateConflict', ['name' => $referralSessionConflict[TableEnum::Name->dbName()]]);

        // Check if another date range falls within this session date range or not
        $referralSessionConflict = ReferralSession::where(TableEnum::Id->dbName(), '!=', $id)
            ->where(TableEnum::StartedAt->dbName(), '>=', $startDateUTC->toDateTimeString())
            ->where(TableEnum::FinishedAt->dbName(), '<=', $finishDateUTC->toDateTimeString())
            ->first();

        if (!is_null($referralSessionConflict))
            return __('thisApp.Errors.Referral.ReferralSessionDateConflict', ['name' => $referralSessionConflict[TableEnum::Name->dbName()]]);

        /********************** Check date conflict END **********************/

        return true;
    }

    /**
     * Validate update
     *
     * @param \App\Models\BackOffice\Referral\ReferralSession $referralSession
     * @return bool|string true: success | string: error message
     */
    private function validateUpdate(ReferralSession $referralSession): bool|string
    {
        $status = $referralSession->getAttribute(TableEnum::Status->dbName());

        if ($status != ReferralSessionStatusEnum::Upcoming->name) {

            if ($referralSession->isDirty($this->getCalculatingFields()))
                return __('thisApp.Errors.Referral.ReferralSessionUpdateOnlyUpcoming', ['status' => ReferralSessionStatusEnum::Upcoming->translate()]);
        }

        return true;
    }

    /**
     * Get the fields involved in the calculations
     *
     * @return array
     */
    private function getCalculatingFields(): array
    {
        return [
            TableEnum::PackageId->dbName(),
            TableEnum::StartedAt->dbName(),
            TableEnum::FinishedAt->dbName(),
        ];
    }
}
