<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    /*
    |--------------------------------------------------------------------------
    | HHH custom Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'custom' => [
        'AccountStatusMessage' => "Your account has been :status.",
        'ContactSupport' => "If needed, Contact support via :email email.",
        'AccessDenied' => 'Access Denied!',

        'Login' => 'Login',
        'SignUp' => 'SIGN UP',

        'SignIn' => 'SIGN IN',
        'SignOut' => 'SIGN OUT',

        'Registration_Title' => 'Registration Form',
        'Registration_Notice' => 'Please enter the information accurately and correctly.',
        'Registration_Agreement' => 'I agree to all Terms & Conditions',
        'Registration_HaveAccount' => 'Already have an account?',

        'placeholder_Username' => 'Username',
        'placeholder_Email' => 'Email',
        'placeholder_Country' => 'Country',
        'placeholder_Password' => 'Password',
        'placeholder_ConfirmPassword' => 'Confirm Password',

        'Username' => 'Username',
        'Password' => 'Password',
        'ConfirmPassword' => 'Confirm Password',
        'Email' => 'Email',


        'LoginForm' => [
            'PageTile'    => 'Login',
            '1thTitle'    => 'Welcome back!',
            '2thTitle'    => 'Happy to see you again!',
            'KeepMeSignedIn' => 'Keep me signed in',
            'ForgotPassword' => 'Forgot password?',
            'DoNotHaveAnAccount' => 'Don\'t have an account?',
        ],

        'ForgotPasswordForm' => [
            'PageTile'    => 'Forgot Password',
            '1thTitle'    => 'Forgot your password?',
            '2thTitle'    => 'No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.',
            'EmailPasswordResetLink' => 'Send Password Recovery Email',
            'ReturnToLoginPage' => 'Return to the login page',
            'PasswordRecoveryEmailSent' => "Password recovery email sent, please refer to your email. If the email is not in your inbox, please check your spam folder.",
            'PasswordRecoveryEmailNotSent' => 'No user found with this email.',
        ],

        'ResetPasswordForm' => [
            'PageTile'    => 'Reset Password',
            '1thTitle'    => 'Reset Password',
            '2thTitle'    => 'Set your new password',
            'SaveNewPassword' => 'Save New Password',
            'UserNotFound' => 'No user found with this email.',
        ],


    ],
];
