<?php

namespace App\Http\Requests\BackOffice\Currencies;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\CurrencyRatesTableEnum as TableEnum;
use App\Enums\General\CurrencyEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\Currencies\CurrencyRate as model;
use App\Rules\General\Restriction\MaximumAllowedDecimalsRule;
use Illuminate\Validation\Rule;

class CurrencyRateRequest extends SuperRequest
{

    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->defaultAuthorize(model::class);
    }

    /******************** Action rules *********************/

    /**
     * Rules for store a newly created resource in storage.
     *
     * @return array
     */
    public function rulesStore(): array
    {
        // Disabled from controller
        return [];
    }

    /**
     * Rules for update the specified resource in storage.
     *
     * @return array
     */
    public function rulesUpdate(): array
    {
        return [

            TableEnum::NameIso->dbName() => [
                'required',
                Rule::in(CurrencyEnum::dynamicRateItems(true)),
                Rule::unique(DatabaseTablesEnum::CurrencyRates->tableName())->ignore($this->id)
            ],

            TableEnum::OneUsdRate->dbName() => [
                'bail', 'nullable', 'numeric',
                'gt:0',
                new MaximumAllowedDecimalsRule(3)
            ],
        ];;
    }

    /**
     * Rules for remove the specified resource from storage.
     *
     * @return array
     */
    public function rulesDestroy(): array
    {
        // Disabled from controller
        return [];
    }

    /******************** Action rules END *********************/

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return $this->addPadToArrayVal(
            [
                TableEnum::NameIso->dbName()    => trans('thisApp.IsoName'),
                TableEnum::OneUsdRate->dbName() => trans('thisApp.AdminPages.CurrencyRates.OneUsdRate'),
                TableEnum::Descr->dbName()      => trans('general.Description'),
            ]
        );
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $oneUsdRateCol = TableEnum::OneUsdRate->dbName();
        $oneUsdRate = str_replace(",", "", $this->input($oneUsdRateCol));
        if (empty($oneUsdRate))
            $oneUsdRate = null;

        $this->merge([
            $oneUsdRateCol => $oneUsdRate,
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        //
    }
}
