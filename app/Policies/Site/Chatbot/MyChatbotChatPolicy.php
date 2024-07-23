<?php

namespace App\Policies\Site\Chatbot;

use App\Enums\Routes\SitePublicRoutesEnum;
use App\Models\Site\Tickets\MyTicket;
use App\Models\User;
use App\Policies\SuperClasses\superPolicy;

class MyChatbotChatPolicy extends superPolicy
{
    /**
     * This function returns the main path of this section,
     * which is specified in the config file('hh_config.php').
     *
     * @return string
     */

    public static function getPermissionRoute(): string
    {
        return SitePublicRoutesEnum::Support_Chatbot->value;
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
        return $user->isClient()
            && self::isChatbotAvailable($user);
    }

    /**
     * Check if there is an active chatbot for user.
     *
     * @param  \App\Models\User $user
     * @return bool
     */
    private static function isChatbotAvailable(User $user): bool
    {
        return $user->getResponsiveChatbotId() === false ? false : true;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Chatbot is avalilabel for all clients even guest user
        return true;
        // return $this->isPermissionValid($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MyTicket $myTicket = null): bool
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
    public function update(User $user, MyTicket $myTicket = null): bool
    {
        return $this->isPermissionValid($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MyTicket $myTicket = null): bool
    {
        return $this->isPermissionValid($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MyTicket $myTicket = null): bool
    {
        return $this->isPermissionValid($user, __FUNCTION__);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MyTicket $myTicket = null): bool
    {
        return $this->isPermissionValid($user, __FUNCTION__);
    }
}
