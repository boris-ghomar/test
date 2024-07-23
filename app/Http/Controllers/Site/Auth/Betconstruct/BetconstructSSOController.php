<?php

namespace App\Http\Controllers\Site\Auth\Betconstruct;


use App\Enums\Routes\SitePublicRoutesEnum;
use App\HHH_Library\general\php\HashHmacHelper;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\FilterClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\ExternalAdminAPI;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class BetconstructSSOController extends Controller
{

    private const  CALLBACK_HASH_KEY = "Fzagnc4yuP4wvCZX";

    /**
     * Login from SSO (Single Sign-on)
     *
     * @param  string $userId
     * @param  string $sessionId
     * @param  string $hash
     * @return void
     */
    public function login(string $userId, string $sessionId, string $hash)
    {

        $data = [
            'userId' => $userId,
            'sessionId' => $sessionId
        ];

        // Check hash
        if (HashHmacHelper::isHashValid(json_encode($data), self::CALLBACK_HASH_KEY, $hash)) {

            // Check session ID
            if (session()->getId() === $sessionId) {

                // Fetch client information from API
                $client = ExternalAdminAPI::getClient([
                    FilterClientModelEnum::Id->name => $userId,
                    FilterClientModelEnum::Gender->name => "1"
                ]);

                if (JsonResponseHelper::isJsonResponseSuccess($client)) {

                    $user = User::find(1);

                    Auth::login($user, true);
                } else {
                    // register error in database
                }
            }
        }

        return redirect(SitePublicRoutesEnum::MainPage->route());
    }
}
