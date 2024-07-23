<?php

namespace App\HHH_Library\general\php;

use App\Enums\Database\Tables\UsersTableEnum;
use App\Models\General\SessionModel;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class  SessionHelper
{
    /**
     * Set Session
     *
     * @param string $key
     * @param  mixed $value
     * @return void
     */
    public static function setSession(string $key, mixed $value): void
    {
        Session::put($key, $value);
        Session::save();
    }

    /**
     * Set Session
     *
     * @param string $key
     * @param  mixed $default
     * @return mixed
     */
    public static function getSession(string $key, mixed $default = null): mixed
    {
        return Session::get($key, $default);
    }

    /**
     * Forget session
     *
     * @param string $key
     * @return void
     */
    public static function forgetSession(string $key): void
    {
        self::setSession($key, null);
        Session::forget($key);
        Session::save();
    }

    /**
     * Delete sessions
     *
     * @param  bool $forgetSessions false ? (only set value null) : (set value null) & (forget key)
     * @param  array $keys
     * @return void
     */
    public static function deleteSessions(bool $forgetSessions = false, string ...$keys): void
    {
        foreach ($keys as $key) {

            self::setSession($key, null);

            if ($forgetSessions)
                self::forgetSession($key);
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
        $user[UsersTableEnum::RememberToken->dbName()] = null;
        $user->save();

        SessionModel::where('user_id', $user->id)->delete();
    }
}
