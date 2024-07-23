<?php

namespace App\Http\Controllers\BackOffice\Auth;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\PasswordResetTokenEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Routes\AdminPublicRoutesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\BackOffice\Auth\PasswordResetAttemptRequest;
use Illuminate\Http\Request;
use App\Models\BackOffice\PeronnelManagement\Personnel;
use App\Models\General\PasswordResetToken;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{

    /**
     * Display login form.
     */
    public function index(Request $request, string $token)
    {
        $this->deleteExpiredTokens();

        $resetPaswordToken = PasswordResetToken::where(PasswordResetTokenEnum::Token->dbName(), $token)
            ->where(PasswordResetTokenEnum::Email->dbName(), $request->input(PasswordResetTokenEnum::Email->dbName()))
            ->first();

        if (is_null($resetPaswordToken))
            abort(404);

        return view('hhh.BackOffice.pages.auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle login request attempt.
     *
     * @param  \App\Http\Requests\BackOffice\Auth\LoginAttemptRequest $request
     * @return void
     */
    public function attempt(PasswordResetAttemptRequest $request)
    {
        $this->deleteExpiredTokens();

        $resetPaswordToken = PasswordResetToken::where(PasswordResetTokenEnum::Token->dbName(), $request->input(PasswordResetTokenEnum::Token->dbName()))
            ->where(PasswordResetTokenEnum::Email->dbName(), $request->input(PasswordResetTokenEnum::Email->dbName()))
            ->first();

        if (is_null($resetPaswordToken))
            return abort(404);

        $personnel = Personnel::where(UsersTableEnum::Email->dbName(), $request->input(UsersTableEnum::Email->dbName()))
            ->first();

        if (!is_null($personnel)) {

            $personnel[UsersTableEnum::Password->dbName()] = Hash::make($request->input(UsersTableEnum::Password->dbName()));

            $personnel->save();
            $resetPaswordToken->delete();

            return redirect(AdminPublicRoutesEnum::Login->route());
        }

        return redirect()->back()->withInput()->withErrors([__('auth.failed')]);
    }

    /**
     * Delete expired tokens, bas on: config('auth.passwords.users.expire')
     *
     * @return void
     */
    private function deleteExpiredTokens()
    {
        PasswordResetToken::where(TimestampsEnum::CreatedAt->dbName(), '<', now()->subMinutes(config('auth.passwords.users.expire')))
            ->delete();
    }
}
