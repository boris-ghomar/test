<?php

namespace App\Http\Controllers\Site\Tickets;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Http\Controllers\SuperClasses\SuperController;
use App\Models\Site\Tickets\MyTicket;

class MyTicketController extends SuperController
{

    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, MyTicket::class);

        $myTickets = MyTicket::Orderby(TimestampsEnum::CreatedAt->dbName(), 'desc')
            ->paginate(15);

        $titles = [
            __('general.ID'),
            __('thisApp.Site.Tickets.Subject'),
            __('thisApp.Site.Tickets.UpdatedAt'),
            __('thisApp.Site.Tickets.Status'),
        ];

        $data = [
            'titles'    => $titles,
            'paginator' => $myTickets,
        ];
        return view('hhh.Site.pages.Tickets.TicketsList.index', $data);
    }

}
