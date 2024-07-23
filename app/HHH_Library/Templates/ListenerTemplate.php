<?php

namespace App\HHH_Library\Templates;

use App\Constants\TryConstants;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Throwable;

class ListenerTemplate
{
    /**
     * The number of times the queued listener may be attempted.
     *
     * @var int
     */
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
     * @param  \Illuminate\Auth\Events\Login $event
     * @return void
     */
    public function handle(Login $event): void
    {
        //
    }

    /**
     * Handle a job failure.
     *
     * @param  \Illuminate\Auth\Events\Login $event
     * @param  Throwable $exception
     * @return void
     */
    public function failed(Login $event, Throwable $exception): void
    {
        //
    }
}
