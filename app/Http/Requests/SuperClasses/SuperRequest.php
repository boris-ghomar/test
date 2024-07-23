<?php

namespace App\Http\Requests\SuperClasses;

use App\HHH_Library\general\php\Enums\HttpMethodEnum;
use App\HHH_Library\general\php\traits\AddAttributesPad;
use App\Http\Requests\traits\authorizeMethods;
use App\Interfaces\Request\SeparateRulesRequestInterface;
use Illuminate\Foundation\Http\FormRequest;

abstract class SuperRequest extends FormRequest implements SeparateRulesRequestInterface
{
    use AddAttributesPad;
    use authorizeMethods;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        switch ($this->method()) {
                // store
            case HttpMethodEnum::POST->name:
                return $this->rulesStore();
                // update
            case HttpMethodEnum::PUT->name:
                return $this->rulesUpdate();
                // destroy
            case HttpMethodEnum::DELETE->name:
                return $this->rulesDestroy();
            default:
                return [];
        }
    }
}
