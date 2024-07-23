<?php

namespace App\Exceptions;

use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        /*************** HHHE **************/


        $this->renderable(function (Exception $exception, $request) {

            // config(['app.debug' => false]); //for test

            // if($exception->getCode() > 1000) this is public message and user can see it
            $showFullMessage = ($exception->getCode() > 1000) ? true : config('app.debug');
            $fe = FlattenException::create($exception);

            if ($request->is('api/*')) {
                //write your logic for api call

                $error = "";
                $message = "";
                $statusCode = 500;
                $headers = [];

                if ($exception instanceof ValidationException) {
                    $statusCode = $exception->status;
                    $error = "validation.exception";
                    $message = $showFullMessage ? $exception->errors() : Arr::flatten($exception->errors());
                } else if ($exception instanceof AuthenticationException) {
                    //
                    $statusCode = HttpResponseStatusCode::Unauthorized->value;
                    $error = $exception->getMessage();
                    $message = trans('exception.' . $exception->getMessage());
                } else if ($exception instanceof ThrottleRequestsException) {

                    $headers = $exception->getHeaders();

                    $statusCode = HttpResponseStatusCode::TooManyRequests->value;
                    $error = $exception->getMessage();

                    $remainingSeconds =  $headers['Retry-After'];
                    $remainingTime = trans_choice('general.TimeDisplay.second', $remainingSeconds, ['value' => $remainingSeconds]);
                    $message = trans('exception.TooManyAttempts', ['time' => $remainingTime]);
                } else {
                    $statusCode = $fe->getStatusCode();

                    $error = HttpResponseStatusCode::getMessageByCode($statusCode);
                    $message = $showFullMessage ? trans('exception.' . $exception->getMessage()) : $error;
                }

                return response()->json([
                    "status"    => "failed",
                    "error"     => $error,
                    "message"   => $message,
                ], $statusCode, $headers);
            } else {
                //write your logic for web call

            }
        });
        /*************** HHHE END **************/
    }
}
