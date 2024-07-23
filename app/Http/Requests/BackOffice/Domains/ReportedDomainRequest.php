<?php

namespace App\Http\Requests\BackOffice\Domains;

use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\Domains\ReportedDomain as model;
use Illuminate\Validation\Rule;

class ReportedDomainRequest extends SuperRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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

            'review' => [
                'required',
                Rule::in(array_keys(__('thisApp.ReportedDomainReview'))),
            ],

        ];
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
                'review' => trans('general.Status'),
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
