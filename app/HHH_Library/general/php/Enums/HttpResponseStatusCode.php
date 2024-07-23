<?php

namespace App\HHH_Library\general\php\Enums;

use App\HHH_Library\general\php\traits\Enums\EnumActions;

/**
 * Based on:
 * https://www.php.net/manual/en/function.http-response-code.php
 * https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#information_responses
 *
 */

enum HttpResponseStatusCode: int
{
    use EnumActions;

    /** HTTP status codes */

        // Informational responses
    case Continue = 100;
    case SwitchingProtocols = 101;
    case Processing = 102;
    case EarlyHints = 103;

        // Successful responses
    case OK = 200;
    case Created = 201;
    case Accepted = 202;
    case NonAuthoritativeInformation = 203;
    case NoContent = 204;
    case ResetContent = 205;
    case PartialContent = 206;
    case MultiStatus = 207;
    case AlreadyReported = 208;
    case IMUsed = 226;

        // Redirection messages
    case MultipleChoices = 300;
    case MovedPermanently = 301;
    case MovedTemporarily = 302;
    case SeeOther = 303;
    case NotModified = 304;
    case UseProxy = 305;
    case SwitchProxy = 306;
    case TemporaryRedirect = 307;
    case PermanentRedirect = 308;

        // Client error responses
    case BadRequest = 400;
    case Unauthorized = 401;
    case PaymentRequired = 402;
    case Forbidden = 403;
    case NotFound = 404;
    case MethodNotAllowed = 405;
    case NotAcceptable = 406;
    case ProxyAuthenticationRequired = 407;
    case RequestTimeout = 408;
    case Conflict = 409;
    case Gone = 410;
    case LengthRequired = 411;
    case PreconditionFailed = 412;
    case PayloadTooLarge = 413;
    case UriTooLong = 414;
    case UnsupportedMediaType = 415;
    case RangeNotSatisfiable = 416;
    case ExpectationFailed = 417;
    case ImATeapot = 418; // 	I'm A Teapot
    case PageExpired = 419;
    case UnprocessableEntity = 422;
    case Locked = 423;
    case FailedDependency = 424;
    case TooEarly = 425;
    case UpgradeRequired = 426;
    case PreconditionRequired = 428;
    case TooManyRequests = 429;
    case RequestHeaderFieldsTooLarge = 431;
    case UnavailableForLegalReasons = 451;

        // Server error responses
    case InternalServerError = 500;
    case NotImplemented = 501;
    case BadGateway = 502;
    case ServiceUnavailable = 503;
    case GatewayTimeout = 504;
    case HttpVersionNotSupported = 505;
    case VariantAlsoNegotiates = 506;
    case InsufficientStorage = 507;
    case LoopDetected = 508;
    case NotExtended = 510;
    case NetworkAuthenticationRequired = 511;


    /**
     * Get HTTP response status code
     *
     * @return string message of code
     */
    public function getMessage(): string
    {

        return match ($this) {

            // Informational responses
            self::Continue => 'Continue',
            self::SwitchingProtocols => 'Switching Protocols',
            self::Processing => 'Processing',
            self::EarlyHints => 'Early Hints',

            // Successful responses
            self::OK => 'OK',
            self::Created => 'Created',
            self::Accepted => 'Accepted',
            self::NonAuthoritativeInformation => 'Non Authoritative Information',
            self::NoContent => 'No Content',
            self::ResetContent => 'Reset Content',
            self::PartialContent => 'Partial Content',
            self::MultiStatus => 'Multi Status',
            self::AlreadyReported => 'Already Reported',
            self::IMUsed => 'IM Used',

            // Redirection messages
            self::MultipleChoices => 'Multiple Choices',
            self::MovedPermanently => 'Moved Permanently',
            self::MovedTemporarily => 'Moved Temporarily',
            self::SeeOther => 'See Other',
            self::NotModified => 'Not Modified',
            self::UseProxy => 'Use Proxy',
            self::SwitchProxy => 'Switch Proxy',
            self::TemporaryRedirect => 'Temporary Redirect',
            self::PermanentRedirect => 'Permanent Redirect',

            // Client error responses
            self::BadRequest => 'Bad Request',
            self::Unauthorized => 'Unauthorized',
            self::PaymentRequired => 'Payment Required',
            self::Forbidden => 'Forbidden',
            self::NotFound => 'Not Found',
            self::MethodNotAllowed => 'Method Not Allowed',
            self::NotAcceptable => 'Not Acceptable',
            self::ProxyAuthenticationRequired => 'Proxy Authentication Required',
            self::RequestTimeout => 'Request Timeout',
            self::Conflict => 'Conflict',
            self::Gone => 'Gone',
            self::LengthRequired => 'Length Required',
            self::PreconditionFailed => 'Precondition Failed',
            self::PayloadTooLarge => 'Payload Too Large',
            self::UriTooLong => 'URI Too Long',
            self::UnsupportedMediaType => 'Unsupported Media Type',
            self::RangeNotSatisfiable => 'Range Not Satisfiable',
            self::ExpectationFailed => 'Expectation Failed',
            self::ImATeapot => 'I\'m a teapot',
            self::PageExpired => 'Page Expired',
            self::UnprocessableEntity => 'Unprocessable Entity',
            self::Locked => 'Locked',
            self::FailedDependency => 'Failed Dependency',
            self::TooEarly => 'Too Early',
            self::UpgradeRequired => 'Upgrade Required',
            self::PreconditionRequired => 'Precondition Required',
            self::TooManyRequests => 'Too Many Requests',
            self::RequestHeaderFieldsTooLarge => 'Request Header Fields Too Large',
            self::UnavailableForLegalReasons => 'Unavailable For Legal Reasons',

            // Server error responses
            self::InternalServerError => 'Internal Server Error',
            self::NotImplemented => 'Not Implemented',
            self::BadGateway => 'Bad Gateway',
            self::ServiceUnavailable => 'Service Unavailable',
            self::GatewayTimeout => 'Gateway Timeout',
            self::HttpVersionNotSupported => 'Http Version Not Supported',
            self::VariantAlsoNegotiates => 'Variant Also Negotiates',
            self::InsufficientStorage => 'Insufficient Storage',
            self::LoopDetected => 'Loop Detected',
            self::NotExtended => 'Not Extended',
            self::NetworkAuthenticationRequired => 'Network Authentication Required',

            default => 'Unknown HTTP response status code'
        };
    }

    /**
    * Get HTTP response status code
    *
    * @param int|null $code (Optinal) if you want message of code
    * @return string message of code
    */
   public static function getMessageByCode(int $code): string
   {

        return self::getCaseByValue($code)->getMessage();
   }
}
