<?php

namespace App\Http\Controllers\Site\UserActions;

use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\Http\Controllers\SuperClasses\SuperController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

abstract class UserActionController extends SuperController
{
    /******************** Implements ********************/

    /**
     * Determine the logic for perform action
     *
     * @param  \Illuminate\Http\Request $request
     * @param mixed $key
     * @return JsonResponse
     */
    public abstract function actionLogic(Request $request, mixed $key): JsonResponse;

    /**
     * Determine internal validation in child class
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\User $user
     * @param  object $key
     * @return object
     */
    protected abstract function internalValidation(Request $request, User $user, object $key): object;

    /******************** Implements END ********************/

    /**
     * Action
     *
     * @param  \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function action(Request $request): JsonResponse
    {
        $key = $this->validateData($request);
        if ($key instanceof JsonResponse)
            return $key;

        return $this->actionLogic($request, $key);
    }

    /**
     * Validate input Data
     *
     * @param  \App\Enums\AccessControl\PostActionsEnum $postActionsEnum
     * @param  \Illuminate\Http\Request $request
     * @return object
     */
    private function validateData(Request $request): object
    {
        $user = User::authUser();

        if (is_null($user))
            return JsonResponseHelper::errorResponse('thisApp.Errors.loginRequired', __('thisApp.Errors.loginRequired'), HttpResponseStatusCode::Unauthorized->value);

        $key = $this->decryptInputData($request->input('key'));

        // Fake data
        if (is_null($key))
            return JsonResponseHelper::errorResponse('general.error.BadRequest', __('general.error.BadRequest'), HttpResponseStatusCode::BadRequest->value);

        return $this->internalValidation($request, $user, $key);
    }

    /**
     * Decrypt input data from request and return as object
     *
     * @param  ?string $key
     * @return object
     */
    private function decryptInputData(?string $key): ?object
    {
        try {
            return json_decode(Crypt::decryptString($key), false);
        } catch (\Throwable $th) {
            return null;
        }
    }
}
