<?php

namespace App\Http\Controllers\Site;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\NotificationsTableEnum as TableEnum;
use App\Http\Controllers\SuperClasses\SuperController;
use App\Models\General\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends SuperController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $notifications = Notification::Orderby(TimestampsEnum::UpdatedAt->dbName(), 'desc')
            ->paginate(15);

        $titles = [
            __('thisApp.Site.Notifications.Date'),
            __('thisApp.Site.Notifications.Title'),
            __('thisApp.Site.Notifications.Message'),
            __('Delete'),
        ];

        $data = [
            'titles'    => $titles,
            'paginator' => $notifications,
        ];

        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }

        return view('hhh.Site.pages.Notifications.index', $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function destroy(Request $request)
    {
        if ($notification = Notification::find($request->input(TableEnum::Id->dbName()))) {

            if ($notification->notifiable_id == auth()->user()->id) {
                // Notification owner can delete it
                $notification->delete();
                return redirect()->back();
            }
        }

        return redirect()->back()->withErrors([trans('general.NotFoundItem')]);
    }

    /**
     * Remove all resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function destroyAll(Request $request)
    {

        if (auth()->check()) {

            /** @var User $user */
            $user = auth()->user();
            $user->notifications()->delete();
        }

        return redirect()->back();
    }
}
