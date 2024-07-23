<?php

namespace App\HHH_Library\general\php;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class  LogCreator
{

    /**
     * The instance itself
     * @var self $instance
     */
    private static $instance;

    private static $modelRequestComparison = null;

    /**
     * Returns the instance of the class.
     *
     * @return \App\HHH_Library\general\php\LogCreator
     */
    private static function getInstance(): self
    {
        // Create it if it doesn't exist.
        return !self::$instance ? new self() : self::$instance;
    }

    /**
     * This function compares the previous information of the model
     * and the new input information in the request and adds it to the log.
     *
     * @param \Illuminate\Foundation\Http\FormRequest|null $request
     * @param \Illuminate\Database\Eloquent\Model|null $model
     * @return \App\HHH_Library\general\php\LogCreator
     */
    public static function attachModelRequestComparison(FormRequest|null $request, Model|null $model): self
    {
        $instance = self::getInstance();

        if (!is_null($request) && !is_null($model)) {

            if ($model->isDirty()) {

                $dirty = $model->getDirty();
                $details = [];
                foreach ($dirty as $key => $value) {

                    $details[$key] = [
                        "Previous" => $model->getOriginal($key),
                        "New" => $request->input($key),
                    ];
                }

                $instance::$modelRequestComparison = json_encode($details, JSON_PRETTY_PRINT);
            }
        }

        return $instance;
    }

    /**
     * Make title
     *
     * @param  string $class __CLASS__
     * @param  string $function __FUNCTION__
     * @return string
     */
    private static function makeTitle(string $class, string $function): string
    {
        return sprintf(
            "%s::%s",
            basename($class),
            $function
        );
    }

    /**
     * This function creates the log text with same structure.
     *
     * @param  string $class __CLASS__
     * @param  string $function __FUNCTION__
     * @param  string $message
     * @param  ?string $title
     * @return string
     */
    private static function createLogText(string $class, string $function, string $message, ?string $title = null): string
    {
        $instance = self::getInstance();

        $logMessage = sprintf(
            "%s\n%s",
            empty($title) ? self::makeTitle($class, $function) : sprintf("%s [Called form: %s]", $title, self::makeTitle($class, $function)),
            $message,
        );

        if (!empty(self::$modelRequestComparison)) {

            $logMessage .= sprintf(
                "\n\nDetails:\n%s",
                $instance::$modelRequestComparison,
            );
        }

        $logMessage .= sprintf(
            "\n\nclass: %s\nfunction: %s",
            $class,
            $function,
        );

        return $logMessage;
    }

    /**
     * This function creates emergency log.
     *
     * @param  string $class __CLASS__
     * @param  string $function __FUNCTION__
     * @param  string $type Emergency|info|etc, all available log types
     * @param  string $message
     * @param  ?string $title
     * @return string
     */
    public static function createLog(string $class, string $function, string $type, string $message, ?string $title = null): string
    {
        $logMessage = "";

        switch ($type) {
            case 'emergency':
                $logMessage = self::createLogEmergency($class, $function, $message, $title);
                break;
            case 'alert':
                $logMessage = self::createLogAlert($class, $function, $message, $title);
                break;
            case 'critical':
                $logMessage = self::createLogCritical($class, $function, $message, $title);
                break;
            case 'error':
                $logMessage = self::createLogError($class, $function, $message, $title);
                break;
            case 'warning':
                $logMessage = self::createLogWarning($class, $function, $message, $title);
                break;
            case 'notice':
                $logMessage = self::createLogNotice($class, $function, $message, $title);
                break;
            case 'info':
                $logMessage = self::createLogInfo($class, $function, $message, $title);
                break;
            case 'debug':
                $logMessage = self::createLogDebug($class, $function, $message, $title);
                break;

            default:
                $logMessage = self::createLogDebug($class, $function, $message, $title);
                break;
        }

        return $logMessage;
    }

    /**
     * This function creates emergency log.
     *
     * @param  string $class __CLASS__
     * @param  string $function __FUNCTION__
     * @param  string $message
     * @param  ?string $title
     * @return string
     */
    public static function createLogEmergency(string $class, string $function, string $message, ?string $title = null): string
    {
        $logMessage = self::getInstance()::createLogText($class, $function, $message, $title);
        Log::emergency($logMessage);

        return $logMessage;
    }

    /**
     * This function creates alert log.
     *
     * @param  string $class __CLASS__
     * @param  string $function __FUNCTION__
     * @param  string $message
     * @param  ?string $title
     * @return string
     */
    public static function createLogAlert(string $class, string $function, string $message, ?string $title = null): string
    {
        $logMessage = self::getInstance()::createLogText($class, $function, $message, $title);
        Log::alert($logMessage);

        return $logMessage;
    }

    /**
     * This function creates critical log.
     *
     * @param  string $class __CLASS__
     * @param  string $function __FUNCTION__
     * @param  string $message
     * @param  ?string $title
     * @return string
     */
    public static function createLogCritical(string $class, string $function, string $message, ?string $title = null): string
    {
        $logMessage = self::getInstance()::createLogText($class, $function, $message, $title);
        Log::critical($logMessage);

        return $logMessage;
    }

    /**
     * This function creates error log.
     *
     * @param  string $class __CLASS__
     * @param  string $function __FUNCTION__
     * @param  string $message
     * @param  ?string $title
     * @return string
     */
    public static function createLogError(string $class, string $function, string $message, ?string $title = null): string
    {
        $logMessage = self::getInstance()::createLogText($class, $function, $message, $title);
        Log::error($logMessage);

        return $logMessage;
    }

    /**
     * This function creates warning log.
     *
     * @param  string $class __CLASS__
     * @param  string $function __FUNCTION__
     * @param  string $message
     * @param  ?string $title
     * @return string
     */
    public static function createLogWarning(string $class, string $function, string $message, ?string $title = null): string
    {
        $logMessage = self::getInstance()::createLogText($class, $function, $message, $title);
        Log::warning($logMessage);

        return $logMessage;
    }

    /**
     * This function creates notice log.
     *
     * @param  string $class __CLASS__
     * @param  string $function __FUNCTION__
     * @param  string $message
     * @param  ?string $title
     * @return string
     */
    public static function createLogNotice(string $class, string $function, string $message, ?string $title = null): string
    {
        $logMessage = self::getInstance()::createLogText($class, $function, $message, $title);
        Log::notice($logMessage);

        return $logMessage;
    }

    /**
     * This function creates info log.
     *
     * @param  string $class __CLASS__
     * @param  string $function __FUNCTION__
     * @param  string $message
     * @param  ?string $title
     * @return string
     */
    public static function createLogInfo(string $class, string $function, string $message, ?string $title = null): string
    {
        $logMessage = self::getInstance()::createLogText($class, $function, $message, $title);
        Log::info($logMessage);

        return $logMessage;
    }

    /**
     * This function creates debug log.
     *
     * @param  string $class __CLASS__
     * @param  string $function __FUNCTION__
     * @param  string $message
     * @param  ?string $title
     * @return void
     */
    public static function createLogDebug(string $class, string $function, string $message, ?string $title = null): string
    {
        $logMessage = self::getInstance()::createLogText($class, $function, $message, $title);
        Log::debug($logMessage);

        return $logMessage;
    }
}
