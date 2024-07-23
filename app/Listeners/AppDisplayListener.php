<?php

namespace App\Listeners;

use App\Constants\TryConstants;
use App\Enums\Session\LocaleKeyEnum;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Throwable;

class AppDisplayListener
{
    /**
     * The number of times the queued listener may be attempted.
     *
     * @var int
     */
    // public $tries = 5;
    public $tries = TryConstants::AppDisplayListener;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  mixed $event
     * @return void
     */
    public function handle(mixed $event): void
    {
        $forceLocaleSession = $event instanceof Login;
        LocaleKeyEnum::setupSessionLocale($forceLocaleSession);
    }

    /**
     * Handle a job failure.
     *
     * @param  mixed $event
     * @param  Throwable $exception
     * @return void
     */
    public function failed(mixed $event, Throwable $exception): void
    {
        //
    }
}
