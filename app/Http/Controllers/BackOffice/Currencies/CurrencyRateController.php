<?php

namespace App\Http\Controllers\BackOffice\Currencies;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\CurrencyRatesTableEnum as TableEnum;
use App\Enums\General\CurrencyEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_Controller;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\Currencies\CurrencyRateRequest;
use App\Http\Resources\BackOffice\Currencies\CurrencyRateCollection;
use App\Http\Resources\BackOffice\Currencies\CurrencyRateResource;
use App\Models\BackOffice\Currencies\CurrencyRate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurrencyRateController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, CurrencyRate::class);

        $this->updateCurrencyRatesTabel();

        $jsGrid_Controller = parent::getJsGridType(CurrencyRate::class, jsGrid_Controller::jsGridType_Edit);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("currencies/currency_rates");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'name'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $attributes = (new CurrencyRateRequest())->attributes();

        $fieldMaker = new jsGrid_FieldMaker('name', __('general.Name'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::NameIso->dbName(), __('thisApp.IsoName'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::OneUsdRate->dbName(), __('thisApp.AdminPages.CurrencyRates.OneUsdRate'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
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
        ];


        return view('hhh.BackOffice.pages.Currencies.CurrencyRates.index', $data);
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
     * @param  \App\Http\Requests\BackOffice\Currencies\CurrencyRateRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function store(CurrencyRateRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     * @param \App\Models\BackOffice\Currencies\CurrencyRate $currencyRate
     */
    public function show(CurrencyRate $currencyRate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param \App\Models\BackOffice\Currencies\CurrencyRate $currencyRate
     */
    public function edit(CurrencyRate $currencyRate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\BackOffice\Currencies\CurrencyRateRequest $request
     * @param \App\Models\BackOffice\Currencies\CurrencyRate $currencyRate
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(CurrencyRateRequest $request, CurrencyRate $currencyRate): JsonResponse
    {
        try {

            $item = CurrencyRate::find($request->input(TableEnum::Id->dbName()));

            $item->fill($request->all());
            $item->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new CurrencyRateResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\Currencies\CurrencyRateRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(CurrencyRateRequest $request): void
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

        $this->authorize(PermissionAbilityEnum::viewAny->name, CurrencyRate::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new CurrencyRateCollection(
            CurrencyRate::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }

    /**
     * Clear deleted enum cases from database table and update base on Enum
     *
     * @return void
     */
    private function updateCurrencyRatesTabel(): void
    {
        $this->clearDeletedData();
        $this->updateData();
    }

    /**
     * Clear settings table from deleted settings in Enums
     *
     * @return void
     */
    private function clearDeletedData(): void
    {
        $dynamicRateNames = CurrencyEnum::dynamicRateItems(true);

        foreach (CurrencyRate::all() as $item) {

            if (!in_array($item[TableEnum::NameIso->dbName()], $dynamicRateNames))
                $item->delete();
        }
    }

    /**
     * Update settings table in database whit new items in enum
     *
     * @return void
     */
    private function updateData(): void
    {
        foreach (CurrencyEnum::dynamicRateItems() as $case) {

            // To avoid of update existing items, only insert not exist item
            if (!CurrencyRate::where(TableEnum::NameIso->dbName(), $case->name)->exists()) {

                $currencyRate = new CurrencyRate();
                $currencyRate[TableEnum::NameIso->dbName()] = $case->name;
                $currencyRate->save();
            }
        }
    }
}
