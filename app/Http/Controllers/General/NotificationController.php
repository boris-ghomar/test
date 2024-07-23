<?php

namespace App\Http\Controllers\General;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\NotificationsTableEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_Controller;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Resources\General\NotificationCollection;
use App\Models\General\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jsGrid_Controller = new jsGrid_Controller("jsGrid", jsGrid_Controller::jsGridType_StaticDelete);
        $jsGrid_Controller->setApiBaseUrl($this->getApiBaseUrl());
        $jsGrid_Controller->setApiSubUrl("notifications/inbox");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, trans('confirm.Delete.simple'));

        $fieldMaker = new jsGrid_FieldMaker("id", null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TimestampsEnum::CreatedAt->dbName(), __('general.Date'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_Width, 120);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker("subject", __('general.Subject'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setItemProperties($fieldMaker::field_Width, 150);
        $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker("message", __('general.Message'));
        $fieldMaker->makeField_Text();

        $fieldMaker->setItemProperties($fieldMaker::field_Width, "auto");
        $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, null);
        $fieldMaker->makeField_Control();
        $jsGrid_Controller->putField($fieldMaker);


        $data = [
            config('hhh_config.keywords.jsGridJavaData')    =>  $jsGrid_Controller->create(),

            "userHasNotification" => boolval(auth()->user()->notifications->count()),
        ];



        return view($this->getView(), $data);
    }

    /**
     * Get view path
     *
     * @return string
     */
    private function getView(): string
    {
        return User::authUser()->isPersonnel() ? 'hhh.BackOffice.pages.Notifications.index' : 'hhh.Site.pages.Notifications.index';
    }
    /**
     * Get api base Url
     *
     * @return string
     */
    private function getApiBaseUrl(): string
    {
        return User::authUser()->isPersonnel() ? url(config('hhh_config.apiBaseUrls.backoffice.javascript')) : url(config('hhh_config.apiBaseUrls.site.javascript'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Notification $notification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notification $notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if ($notification = Notification::find($request->input(NotificationsTableEnum::Id->dbName()))) {

            if ($notification->notifiable_id == auth()->user()->id) {
                // Notification owner can delete it
                $notification->delete();
                return JsonResponseHelper::successResponse(null, trans('general.ItemSuccessfullyRemoved'));
            }
        }

        return JsonResponseHelper::errorResponse(null, trans('general.NotFoundItem'), HttpResponseStatusCode::BadRequest->value);
    }

    /**
     * Remove all resource from storage.
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

    /**
     * Return the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function apiIndex(Request $request)
    {
        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        $collection =  new NotificationCollection(
            $pageNotifications = Notification::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );

        foreach ($pageNotifications as $notification) {
            $notification->markAsRead();
        }

        return $collection;
    }
}
