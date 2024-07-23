<?php

namespace App\Http\Requests\BackOffice\Settings;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\Settings\Setting as model;


class SettingRequest extends SuperRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can(PermissionAbilityEnum::update->name, model::class);;
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
        $rules = [];

        // $inputs
        foreach ($this->input() as $key => $value) {

            if (AppSettingsEnum::hasName($key)) {

                /** @var AppSettingsEnum $case */
                $case = AppSettingsEnum::getCase($key);

                $rules[$case->name] = $case->validationRules(true);
            }
        }

        // Files
        foreach ($this->allFiles() as $key => $value) {

            if (AppSettingsEnum::hasName($key)) {

                /** @var AppSettingsEnum $case */
                $case = AppSettingsEnum::getCase($key);

                $rules[$case->name] = $case->validationRules(true);
            }
        }

        return $rules;
    }

    /**
     * Rules for remove the specified resource from storage.
     *
     * @return array
     */
    public function rulesDestroy(): array
    {
        // disabled from controller
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
        $attributes = [];
        $formAttributes = __('PagesContent_GeneralSettings.form');

        foreach ($formAttributes as $attr => $details) {

            $attributes[$attr] = $details['name'];
        }

        return $this->addPadToArrayVal($attributes);
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        //
    }
}
