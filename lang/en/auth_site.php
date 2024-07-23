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

        'placeholder_Username' => 'Username on the Betcart site',
        'placeholder_Email' => 'Email',
        'placeholder_Mobile' => 'Mobile Number',
        'placeholder_Country' => 'Country',
        'placeholder_Password' => 'Password on the Betcart site',
        'placeholder_ConfirmPassword' => 'Confirm Password',

        'Username' => 'Username on the Betcart site',
        'Password' => 'Password on the Betcart site',
        'ConfirmPassword' => 'Confirm Password',
        'Email' => 'Email',
        'MobileNumber' => 'Mobile Number',
        'VerificationCode' => 'Verification Code',


        'LoginForm' => [
            'PageTile'    => 'Login',
            '1thTitle'    => 'Login to account',
            '2thTitle'    => 'Always remember your username and password',
            'KeepMeSignedIn' => 'Keep me signed in',
            'ForgotPassword' => 'Forgot password?',
            'DoNotHaveAnAccount' => 'Don\'t have a Betcart account?',
        ],

        'Registration' => [
            'PageTile'    => 'Registration Form',
        ],

        'ForgotPasswordForm' => [
            'PageTile'    => 'Forgot Password',
            '1thTitle'    => 'Forgot your password?',
            '2thTitle'    => 'No problem, choose one of the password recovery methods to recover your password.',
            'EmailPasswordResetLink' => 'Send Password Recovery Email',
            'ReturnToLoginPage' => 'Return to the login page',
            'PasswordRecoveryEmailSent' => "Password recovery email sent, please refer to your email. If the email is not in your inbox, please check your spam folder.",
            'PasswordRecoveryEmailNotSent' => 'No user found with this email.',
            'SendVerificationCode' => 'Send Verification Code',
            'Authentication' => 'Authentication',
            'ResetPassword' => 'Reset Password',

            'errors' => [
                'accountNotFound' => 'There is no user account with this profile.',
                'invalidPasswordRecoveryMethod' => 'The password recovery method is invalid.',
                'verificationMobileNotReceived' => 'The verification code has been sent to your mobile number: :verifiable via SMS, if you do not receive the SMS, you can try again after :remainingTime.',
                'verificationEmailNotReceived' => 'The verification code has been sent to your email :verifiable, if you do not receive the email, you can try again after :remainingTime.',
                'verificationFailed' => 'The entered code is incorrect or has expired.',
            ],

            'messages' => [
                'successfullyReset' => 'Your password has been successfully reset.',
            ],

            'index' => [
                'RecoveryMethod' => [
                    'name' => 'Password recovery method',
                    'placeholder' => '',
                    'notice' => '',
                ],
            ],

            'attempByEmail' => [
                'notice' => 'Enter your email address and we will send you a confirmation code that will allow you to choose a new password.',
            ],

            'attempByMobile' => [
                'notice' => 'Enter your mobile number and we will send you a confirmation code that will allow you to choose a new password.',
                'mobileDescr' => 'Enter your mobile number along with the country code. (example: 0016471231234)',
            ],

            'verification' => [
                'notice' => 'Enter the confirmation code you received in this field.',
            ],

            'resetPassword' => [
                'notice' => 'Enter your new password.',
            ],
        ],

        'ResetPasswordForm' => [
            'PageTile'    => 'Reset Password',
            '1thTitle'    => 'Reset Password',
            '2thTitle'    => 'Set your new password',
            'SaveNewPassword' => 'Save New Password',
            'UserNotFound' => 'User with this credentials was not found!',
        ],

    ],
];
