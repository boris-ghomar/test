<?php

namespace App\Http\Controllers\BackOffice\Auth;

use App\Enums\Database\Tables\UsersTableEnum as TableEnum;
use App\Enums\Routes\AdminPublicRoutesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\BackOffice\Auth\LoginAttemptRequest;
use App\Models\BackOffice\PeronnelManagement\Personnel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{

    /**
     * Display login form.
     */
    public function index()
    {
        return view('hhh.BackOffice.pages.auth.login');
    }

    /**
     * Handle login request attempt.
     *
     * @param  \App\Http\Requests\BackOffice\Auth\LoginAttemptRequest $request
     * @return void
     */
    public function attempt(LoginAttemptRequest $request)
    {

        $personnel = Personnel::where(TableEnum::Username->dbName(), $request->input(TableEnum::Username->dbName()))->first();

        if (!is_null($personnel)) {

            if (Hash::check($request->input(TableEnum::Password->dbName()), $personnel[TableEnum::Password->dbName()])) {

                Auth::login($personnel, $request->input('remember'));
                return redirect(AdminPublicRoutesEnum::Dashboard->route());
            }
        }

        return redirect()->back()->withInput()->withErrors([__('auth.failed')]);
    }
}
