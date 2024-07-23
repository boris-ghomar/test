<?php

namespace App\Http\Controllers\BackOffice\Domains;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\DomainCategoriesTableEnum;
use App\Enums\Database\Tables\DomainHolderAccountsTableEnum;
use App\Enums\Database\Tables\DomainHoldersTableEnum;
use App\Enums\Database\Tables\DomainsTableEnum as TableEnum;
use App\Enums\Domains\DomainStatusEnum;
use App\Enums\Routes\AdminPublicRoutesEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\general\php\LogCreator;
use App\Http\Controllers\SuperClasses\SuperController;
use App\Http\Requests\BackOffice\Domains\DomainImportRequest;
use App\Models\BackOffice\Domains\Domain;
use App\Models\BackOffice\Domains\DomainCategory;
use App\Models\BackOffice\Domains\DomainHolderAccount;
use App\Models\BackOffice\Domains\DomainImport;
use App\Models\User;
use App\Rules\General\StringPattern\EnglishString;
use App\Rules\General\User\DateTimeFormatRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rule;

class DomainImportController extends SuperController
{

    protected const PAYLOAD_KEY = "payload";
    protected const USER_ID_KEY = "userId";

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, DomainImport::class);

        // domainCategoriesCollection
        $domainCategoriesCollection = DropdownListCreater::makeByModel(DomainCategory::class, DomainCategoriesTableEnum::Name->dbName())
            ->sort(true)->get();


        // domainHolderAccountsCollection
        $domainHolderAccounts = DomainHolderAccount::all();

        $domainHolderAccountsCollection = [];
        foreach ($domainHolderAccounts as $domainHolderAccount) {

            $name = sprintf(
                "%s: %s",
                $domainHolderAccount->domainHolder[DomainHoldersTableEnum::Name->dbName()],
                $domainHolderAccount[DomainHolderAccountsTableEnum::Username->dbName()]
            );

            $domainHolderAccountsCollection[$name] = $domainHolderAccount->id;
        }

        $data = [
            self::PAYLOAD_KEY   => $this->makePayload(),
            'domainCategoriesCollection' => $domainCategoriesCollection,
            'domainHolderAccountsCollection' => $domainHolderAccountsCollection,
        ];

        return view('hhh.BackOffice.pages.Domains.ImportDomains.index', $data);
    }

    /**
     * Make user hash
     *
     * @param  \App\Models\User|null $user
     * @return string|Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    protected function makePayload(?User $user = null): string|RedirectResponse
    {
        if (is_null($user))
            $user = auth()->user();

        // The user was not identified
        if (is_null($user))
            return redirect(AdminPublicRoutesEnum::Login->route());

        $idCol = "id";

        $payloadData = [
            self::USER_ID_KEY => $user->$idCol,
        ];

        $payload = Crypt::encrypt(json_encode($payloadData));

        return $payload;
    }

    /**
     * Extract payload data
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function extractPayloadData(Request $request): JsonResponse|null
    {

        try {

            $accessDenied = JsonResponseHelper::errorResponse('PagesContent_ImportDomains.messages.accessDenied', __('PagesContent_ImportDomains.messages.accessDenied'), HttpResponseStatusCode::Forbidden->value, ['refreshPage' => true]);

            if (!$request->has(self::PAYLOAD_KEY))
                return $accessDenied;

            $payload = $request->input(self::PAYLOAD_KEY);
            if (empty($payload))
                return $accessDenied;

            $payloadData = json_decode(Crypt::decrypt($payload), true);

            if (isset($payloadData[self::USER_ID_KEY])) {

                if (User::authUser()->id != $payloadData[self::USER_ID_KEY])
                    return $accessDenied;
            } else
                return $accessDenied;
        } catch (\Throwable $th) {

            $errorMessage = sprintf(
                "Request: %s\nError: %s",
                json_encode($request->all()),
                $th->getMessage()
            );
            LogCreator::createLogAlert(get_class(), __FUNCTION__, $errorMessage, "Payload Error");
        }

        return null;
    }

    /**
     * Import domains list
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(DomainImportRequest $request): JsonResponse
    {
        $payloadExtractionResult =  $this->extractPayloadData($request);
        if (!is_null($payloadExtractionResult))
            return $payloadExtractionResult;

        if (!$request->has('domains'))
            return JsonResponseHelper::errorResponse('PagesContent_ImportDomains.messages.NoDataMsg', __('PagesContent_ImportDomains.messages.NoDataMsg'), HttpResponseStatusCode::FailedDependency->value);

        try {

            $data = [];

            $overwrite = $request->input('Overwrite');
            $domains = json_decode($request->input('domains'), true);

            if (empty($domains))
                return JsonResponseHelper::errorResponse('PagesContent_ImportDomains.messages.NoDataMsg', __('PagesContent_ImportDomains.messages.NoDataMsg'), HttpResponseStatusCode::FailedDependency->value);

            foreach ($domains as $item) {

                $item = $this->prepareDomainData($item);
                $errors = $this->validateDomainData($item);
                if ($errors === true) {

                    $domain = Domain::where(TableEnum::Name->dbName(), $item[TableEnum::Name->dbName()])->first();

                    $allowToImport = false;

                    if (is_null($domain)) {
                        // New item
                        $item['result'] = [
                            "status" => "Success",
                            "message" => __('PagesContent_ImportDomains.messages.StoredMsg')
                        ];
                        $domain = new Domain();
                        $allowToImport = true;
                    } else if ($overwrite) {
                        // Item exists and allowed to overwrite
                        $allowToImport = true;
                        $item['result'] = [
                            "status" => "Success",
                            "message" => __('PagesContent_ImportDomains.messages.UpdatedMsg')
                        ];
                    } else {
                        // Item exists and not allowed to overwrite
                        $item['result'] = [
                            "status" => "Ignored",
                            "message" => __('PagesContent_ImportDomains.messages.IgnoredMsg')
                        ];
                    }
                    if ($allowToImport) {

                        $domain->fill($request->all());
                        $domain->fill($item);
                        $domain->converDatesToUTC();
                        $domain->save();
                    }
                } else {
                    $item['result'] = [
                        "status" => "Failed",
                        "message" => array_values($errors->toArray())
                    ];
                }

                array_push($data, $item);
            }

            return JsonResponseHelper::successResponse($this->makeResponseData(["importResults" => $data]), 'Success');
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }
    }

    /**
     * Prepare incomming domain data for validation and store
     *
     * @param  ?array $domainData
     * @return ?array
     */
    private function prepareDomainData(?array $domainData): ?array
    {
        if (empty($domainData))
            return $domainData;

        // Convert boolean values to actual boolean
        $booleanAttributes = [
            TableEnum::AutoRenew->dbName(),
        ];

        $boolTrue = ["yes", "true", "1"];
        $boolFalse = ["no", "false", "0"];
        foreach ($booleanAttributes as $attr) {

            if (isset($domainData[$attr])) {

                if (in_array(strtolower($domainData[$attr]), $boolTrue))
                    $domainData[$attr] = true;
                else if (in_array(strtolower($domainData[$attr]), $boolFalse))
                    $domainData[$attr] = false;
            }
        }

        // Convert status value to status key
        $status = $domainData[TableEnum::Status->dbName()];
        $statusTranslations = __('thisApp.Enum.DomainStatusEnum');
        $statusKey = array_search($status, $statusTranslations);
        if ($statusKey !== false)
            $domainData[TableEnum::Status->dbName()] = $statusKey;

        return $domainData;
    }

    /**
     * validateDomainData
     *
     * @param  array $domainData
     * @return bool|Illuminate\Support\MessageBag
     */
    private function validateDomainData(array $domainData): bool|MessageBag
    {
        $attributes = (new DomainImportRequest())->attributes();

        $validator = Validator::make($domainData, [

            TableEnum::Name->dbName() => [
                'bail', 'required',
                new EnglishString,
            ],

            TableEnum::AutoRenew->dbName() => ['boolean'],
            TableEnum::Status->dbName() => [
                'required',
                Rule::in(DomainStatusEnum::names()),
            ],

            TableEnum::RegisteredAt->dbName() => ['bail', 'nullable', new DateTimeFormatRule],
            TableEnum::ExpiresAt->dbName() => ['bail', 'nullable', new DateTimeFormatRule],
            TableEnum::AnnouncedAt->dbName() => ['bail', 'nullable', new DateTimeFormatRule],
            TableEnum::BlockedAt->dbName() => ['bail', 'nullable', new DateTimeFormatRule],
        ], [], $attributes)->stopOnFirstFailure(true);

        if ($validator->fails())
            return $validator->errors();

        return true;
    }


    /**
     * Attach default data and make final response data
     *
     * @param  ?array $data
     * @return array
     */
    protected function makeResponseData(?array $data): array
    {
        $defaultData = [
            'csrfToken'         => csrf_token(),
            'debugMode'         => (bool) config('app.debug'),
        ];

        return (is_null($data)) ? $defaultData : array_merge($defaultData, $data);
    }
}
