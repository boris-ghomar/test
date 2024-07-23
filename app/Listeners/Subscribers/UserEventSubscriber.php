<?php

namespace App\Listeners\Subscribers;

use App\Listeners\AppDisplayListener;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;

class UserEventSubscriber
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle user login events.
     */
    public function handleUserLogin(Login $event): void
    {
    }

    /**
     * Handle user logout events.
     */
    public function handleUserLogout(Logout $event): void
    {
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [

            Login::class => [
                [AppDisplayListener::class, 'handle'],
                'handleUserLogin',
            ],

            Logout::class => 'handleUserLogout',
        ];
    }
}
