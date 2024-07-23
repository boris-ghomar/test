<?php

namespace App\HHH_Library\general\php\traits\Enums;

use App\HHH_Library\general\php\SessionHelper;
use App\Models\User;

trait  EnumSession
{
    /**
     * Set Session
     *
     * @param  mixed $value
     * @return void
     */
    public function setSession(mixed $value): void
    {
        SessionHelper::setSession($this->value, $value);
    }

    /**
     * Set Session
     *
     * @param  mixed $default
     * @return mixed
     */
    public function getSession(mixed $default = null): mixed
    {
        return SessionHelper::getSession($this->value, $default);
    }

    /**
     * Forget session
     *
     * @return void
     */
    public function forgetSession(): void
    {
        SessionHelper::forgetSession($this->value);
    }

    /**
     * Delete sessions
     *
     * @param  bool $forgetSessions false ? (only set value null) : (set value null) & (forget key)
     * @param  self $items if empty => All sessions will be deleted
     * @return void
     */
    public static function deleteSessions(bool $forgetSessions = false, self ...$items): void
    {
        $items = count($items) > 0 ? $items : self::cases();

        foreach ($items as $item) {

            $item->setSession(null);

            if ($forgetSessions)
                $item->forgetSession();
        }
    }

    /**
     * Logout user from all devices
     *
     * @param  \App\Models\User $user
     * @return void
     */
    public static function logoutUserFromAllDevices(User $user): void
    {
        SessionHelper::logoutUserFromAllDevices($user);
    }
}
