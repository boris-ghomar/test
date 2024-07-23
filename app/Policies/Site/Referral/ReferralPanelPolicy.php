<?php

namespace App\Policies\Site\Referral;

use App\Enums\Routes\SiteRoutesEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\Jobs\FetchData\Single\FetchClientExtraDataJob;
use App\Models\Site\Referral\ReferralPanel;
use App\Models\User;
use App\Policies\SuperClasses\superPolicy;

class ReferralPanelPolicy extends superPolicy
{
    /**
     * This function returns the main path of this section,
     * which is specified in the config file('hh_config.php').
     *
     * @return string
     */

    public static function getPermissionRoute(): string
    {
        return SiteRoutesEnum::Referral_Panel->value;
    }

    /**
     * This function determines whether this route
     * is relevant to the user or not.
     * This permission is a priority for all permissions,
     * and even if this permission is given to the user
     * in the permission section, this function can violate it.
     *
     * @return boolean
     */
    public static function isRelatedToUser(User $user): bool
    {
        if (!$user->isClient())
            return false;

        if (AppSettingsEnum::ReferralIsActive->getValue())
            return true;

        $clientExtra = $user->userExtra;

        if (is_null($clientExtra)) {

            FetchClientExtraDataJob::dispatchSync($user->id);
            $clientExtra = $user->userExtra;
        }

        return AppSettingsEnum::ReferralIsActiveForTestClients->getValue()
            && $clientExtra[ClientModelEnum::IsTest->dbName()];
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->isPermissionValid($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ReferralPanel $referralPanel = null): bool
    {
        return $this->isPermissionValid($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->isPermissionValid($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ReferralPanel $referralPanel = null): bool
    {
        return $this->isPermissionValid($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ReferralPanel $referralPanel = null): bool
    {
        return $this->isPermissionValid($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ReferralPanel $referralPanel = null): bool
    {
        return $this->isPermissionValid($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ReferralPanel $referralPanel = null): bool
    {
        return $this->isPermissionValid($user, __FUNCTION__);
    }
}
