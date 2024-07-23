<?php

namespace App\Http\Controllers\BackOffice\ClientsManagement;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\ClientTrustScoresTableEnum as TableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\hhh_Exports\ClientsManagement\ClientTrustScoreExport;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_Controller;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\Models\BackOffice\ClientsManagement\ClientTrustScore;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\ClientsManagement\ClientTrustScoreRequest;
use App\Http\Resources\BackOffice\ClientsManagement\ClientTrustScoreCollection;
use App\Models\BackOffice\ClientsManagement\ClientCategory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientTrustScoreController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, ClientTrustScore::class);

        $jsGrid_Controller = parent::getJsGridType(ClientTrustScore::class, jsGrid_Controller::jsGridType_Edit);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("clients_management/trust_scores");
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
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "130");
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('betconstruct_id', __('thisApp.BetconstructId'));
        $fieldMaker->makeField_Number();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('username', __('general.UserName'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(UsersTableEnum::RoleId->dbName(), __("general.Category"));
        $options = DropdownListCreater::makeByModel(ClientCategory::class, RolesTableEnum::Name->dbName())
            ->prepend("", -1)->useLable("name", "id")->sort(true)->get();
        $fieldMaker->makeField_Select('id', 'name', -1, 'number', $options);
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Score->dbName(), __('thisApp.AdminPages.ClientsManagement.TrustScore'));
        $fieldMaker->makeField_NumberRange();
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::DomainSuspicious->dbName(), __('thisApp.AdminPages.ClientsManagement.DomainSuspicious'));
        $fieldMaker->makeField_NumberRange();
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::DepositCount->dbName(), __('bc_api.DepositCount'));
        $fieldMaker->makeField_NumberRange();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Balance->dbName(), __('bc_api.Balance'));
        $fieldMaker->makeField_NumberRange();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(ClientModelEnum::CurrencyId->dbName(), __('bc_api.CurrencyId'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
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
            config('hhh_config.keywords.useExcelExport')    => User::authUser()->can(PermissionAbilityEnum::export->name, ClientTrustScore::class),
        ];


        return view('hhh.BackOffice.pages.ClientsManagement.ClientTrustScores.index', $data);
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
     * @param  \App\Http\Requests\BackOffice\ClientsManagement\ClientTrustScoreRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function store(ClientTrustScoreRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     * @param \App\Models\BackOffice\ClientsManagement\ClientTrustScore $clientTrustScore
     */
    public function show(ClientTrustScore $clientTrustScore)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param \App\Models\BackOffice\ClientsManagement\ClientTrustScore $clientTrustScore
     */
    public function edit(ClientTrustScore $clientTrustScore)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\ClientsManagement\ClientTrustScoreRequest $request
     * @param \App\Models\BackOffice\ClientsManagement\ClientTrustScore $clientTrustScore
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(ClientTrustScoreRequest $request, ClientTrustScore $clientTrustScore): JsonResponse
    {
        try {
            $item = ClientTrustScore::find($request->input(TableEnum::Id->dbName()));

            $item->fill($request->all());
            $item->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse($request->all(), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\ClientsManagement\ClientTrustScoreRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(ClientTrustScoreRequest $request)
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

        $this->authorize(PermissionAbilityEnum::viewAny->name, ClientTrustScore::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new ClientTrustScoreCollection(
            ClientTrustScore::ApiIndexCollection($request->input())
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
        $this->authorize(PermissionAbilityEnum::export->name, ClientTrustScore::class);

        $exporter = new ClientTrustScoreExport($request->all());
        return $exporter->export();
    }
}
