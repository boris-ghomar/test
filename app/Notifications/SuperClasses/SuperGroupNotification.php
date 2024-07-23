<?php

namespace App\Notifications\SuperClasses;

use App\Models\back_office\workgroup;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notification;

abstract class SuperGroupNotification
{
    private Notification $notification;

    /*********************** Implements ***********************/

    /**
     * NotifiableGroup
     * Return collection of notiables.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    protected abstract function notifiableGroup(): Collection;

    /*********************** Implements END ***********************/

    /**
     * Create a new notification instance.
     *
     * @param [object of Notification Class] $notification :: like as new SystemRuntimeError()
     * @return void
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }


    /**
     * Send notification to 'SystemAdmin' workgroup's users.
     * If you do not want notifications to be sent to some users, you can exclude them.
     *
     * @param array $excludeNotifiableIds
     *                  :: An array of IDs of users for whom you do not want to be notified.
     * @return void
     */
    public function send(array $excludeNotifiableIds = []): void
    {

        foreach ($this->notifiableGroup() as $member) {

            if (!in_array($member->id, $excludeNotifiableIds))
                $member->notify($this->notification);
        }
    }
}
