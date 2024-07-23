<?php

namespace App\Http\Requests\BackOffice\Domains;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\Domains\DomainExtension;
use App\Models\BackOffice\Domains\DomainGenerator as model;
use App\Rules\General\Database\ExistsItem;

class DomainGeneratorRequest extends SuperRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can(PermissionAbilityEnum::viewAny->name, model::class);
    }

    /******************** Action rules *********************/

    /**
     * Rules for store a newly created resource in storage.
     *
     * @return array
     */
    public function rulesStore(): array
    {
        return [

            "DomainCount" => ['bail', 'required', 'numeric', 'min:1', 'max:600'],
            "DomainLettersCount" => ['bail', 'required', 'numeric', 'min:7', 'max:30'],
            "DomainExtension" => ['bail', 'required', 'numeric', new ExistsItem(DomainExtension::class)],
        ];
    }

    /**
     * Rules for update the specified resource in storage.
     *
     * @return array
     */
    public function rulesUpdate(): array
    {
        return $this->rulesStore();
    }

    /**
     * Rules for remove the specified resource from storage.
     *
     * @return array
     */
    public function rulesDestroy(): array
    {
        return [];
    }

    /******************** Action rules END *********************/

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return $this->addPadToArrayVal(
            [
                "DomainCount" => trans('PagesContent_DomainGenerator.form.DomainCount.name'),
                "DomainLettersCount" => trans('PagesContent_DomainGenerator.form.DomainLettersCount.name'),
                "ExcludeLetters" => trans('PagesContent_DomainGenerator.form.ExcludeLetters.name'),
                "DomainExtension" => trans('PagesContent_DomainGenerator.form.DomainExtension.name'),
            ]
        );
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
