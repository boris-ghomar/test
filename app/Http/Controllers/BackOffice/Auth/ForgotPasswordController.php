<?php

namespace App\Http\Controllers\BackOffice\Auth;

use App\Enums\Database\Tables\PasswordResetTokenEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\BackOffice\Auth\ForgotPasswordRequest;
use App\Models\BackOffice\PeronnelManagement\Personnel;
use App\Models\General\PasswordResetToken;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{

    /**
     * Display forgot password form.
     */
    public function index()
    {
        return view('hhh.BackOffice.pages.auth.forgot-password');
    }

    /**
     * Handle forgot password request attempt.
     *
     * @param  \App\Http\Requests\BackOffice\Auth\ForgotPasswordRequest $request
     * @return void
     */
    public function attempt(ForgotPasswordRequest $request)
    {
        $personnel = Personnel::where(UsersTableEnum::Email->dbName(), $request->input(UsersTableEnum::Email->dbName()))
            ->first();

        if (!is_null($personnel)) {

            $passwordResetToken = PasswordResetToken::where(TableEnum::Email->dbName(), $request->input(TableEnum::Email->dbName()))
                ->first();

            if (is_null($passwordResetToken)) {

                $passwordResetToken = PasswordResetToken::create([
                    TableEnum::Email->dbName() => $request->input(TableEnum::Email->dbName()),
                    TableEnum::Token->dbName() => Str::random(64),
                ]);
            }

            $personnel->sendPasswordResetNotification($passwordResetToken[TableEnum::Token->dbName()]);

            return redirect()->back()->withInput()->with('success', __('auth.custom.ForgotPasswordForm.PasswordRecoveryEmailSent'));
        }

        return redirect()->back()->withInput()->withErrors([__('auth.custom.ForgotPasswordForm.PasswordRecoveryEmailNotSent')]);
    }
}
