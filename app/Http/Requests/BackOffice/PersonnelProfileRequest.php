<?php

namespace App\Http\Requests\BackOffice;

use App\Enums\Database\Tables\PersonnelExtrasTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Resources\ImageConfigEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\Enums\Users\UsersTypesEnum;
use App\HHH_Library\general\php\Enums\PregPatternValidationEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\PeronnelManagement\PersonnelExtra;
use App\Models\User;
use App\Rules\General\Database\UniqueSuperKey;
use App\Rules\General\Restriction\AllowedGender;
use App\Rules\General\StringPattern\MinOneLowercase;
use App\Rules\General\StringPattern\MinOneNumber;
use App\Rules\General\StringPattern\MinOneUppercase;
use Illuminate\Validation\Rule;

class PersonnelProfileRequest extends SuperRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = User::authUser();
        return ($this->id === $user->id && $user->isPersonnel());
    }

    /******************** Action rules *********************/

    /**
     * Rules for store a newly created resource in storage.
     *
     * @return array
     */
    public function rulesStore(): array
    {
        // disabled from controller
        return [];
    }

    /**
     * Rules for update the specified resource in storage.
     *
     * @return array
     */
    public function rulesUpdate(): array
    {
        $tabpanel = $this->_tabpanel;

        if ($tabpanel == "Personal") {

            return [
                UsersTableEnum::Email->dbName() => [
                    'bail', 'required', 'email:rfc,strict',
                    "min:" . config('hhh_config.validation.minLength.email'),
                    "max:" . config('hhh_config.validation.maxLength.email'),
                    new UniqueSuperKey(User::class, $this[UsersTableEnum::Id->dbName()], [
                        UsersTableEnum::Email->dbName() => $this[UsersTableEnum::Email->dbName()],
                        UsersTableEnum::Type->dbName() => UsersTypesEnum::Personnel->name,
                    ]),
                ],
                PersonnelExtrasTableEnum::FirstName->dbName() => ['required', 'alpha'],
                PersonnelExtrasTableEnum::LastName->dbName()  => ['required', 'alpha'],
                PersonnelExtrasTableEnum::AliasName->dbName()  => [
                    'nullable',
                    Rule::unique(PersonnelExtra::class)->ignore(auth()->user()->personnel->personnelExtra->id)
                ],
                PersonnelExtrasTableEnum::Gender->dbName()  => ['required', new AllowedGender],
            ];
        } else if ($tabpanel == "Photo") {

            $profilePhoto = ImageConfigEnum::ProfilePhoto;

            return [

                UsersTableEnum::ProfilePhotoName->dbName() => [
                    'image',
                    "mimes:" . $profilePhoto->mimes(),
                    sprintf("dimensions:min_width=%s,min_height=%s", $profilePhoto->minWidth(), $profilePhoto->minHeight()),
                    sprintf("dimensions:max_width=%s,max_height=%s", $profilePhoto->maxWidth(), $profilePhoto->maxHeight()),
                    "min:" . $profilePhoto->minSize(),
                    "max:" . $profilePhoto->maxSize(),
                ],
            ];
        } else if ($tabpanel == "Password") {

            return [
                /**
                 * password:web
                 * https://laravel.com/docs/8.x/validation#rule-password
                 *
                 * authentication guard, comes from: '\config\auth.php'
                 */
                'current_password' => [
                    'nullable',
                    'required_with:new_password',
                    'different:new_password',
                    'current_password:web',
                ],
                'new_password' => [
                    'nullable',
                    'min:' . config('hhh_config.validation.minLength.password'),
                    'max:' . config('hhh_config.validation.maxLength.password'),
                    new MinOneLowercase,
                    new MinOneUppercase,
                    new MinOneNumber,
                ],
                'new_password_confirmation' => [
                    'required_with:new_password',
                    'same:new_password'
                ],
            ];
        } else if ($tabpanel == "Settings") {

            $case = AppSettingsEnum::AdminPanelTimeZone;
            $rules[$case->name] = ['nullable', PregPatternValidationEnum::Timezone->regex()];

            $case = AppSettingsEnum::AdminPanelCalendarType;
            $rules[$case->name] = ['nullable'];

            return $rules;
        }

        return [];
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
        $formAttributes = __('PagesContent_PersonnelProfile.form');

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

        $this->merge([
            UsersTableEnum::Id->dbName() => $this->user()->id
        ]);
    }
}
