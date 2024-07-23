<?php

namespace App\Http\Controllers\BackOffice\Tickets;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Database\Tables\TicketsTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Tickets\TicketPrioritiesEnum;
use App\Enums\Tickets\TicketsStatusEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_Controller;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\Tickets\TicketRequest;
use App\Http\Resources\BackOffice\Tickets\TicketResource;
use App\Http\Resources\BackOffice\Tickets\TicketsCollection;
use App\Models\BackOffice\ClientsManagement\ClientCategory;
use App\Models\BackOffice\Tickets\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::authUser();

        $this->authorize(PermissionAbilityEnum::viewAny->name, Ticket::class);

        $jsGrid_Controller = parent::getJsGridType(Ticket::class, jsGrid_Controller::jsGridType_EditDelete);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("tickets/all_tickets");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'id'])));

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $attributes = (new TicketRequest())->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), __('thisApp.AdminPages.Tickets.TicketID'));
        $fieldMaker->makeField_Number();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "140");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::OwnerId->dbName(), __('thisApp.UserId'));
        $fieldMaker->makeField_Number();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "140");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('betconstruct_id', __('thisApp.BetconstructId'));
        $fieldMaker->makeField_Number();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "150");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('betconstruct_username', __('general.UserName'));
        $fieldMaker->makeField_text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "120");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('client_category_id', __("thisApp.ClientCategory"));
        $options = DropdownListCreater::makeByModel(ClientCategory::class, RolesTableEnum::Name->dbName())
            ->prepend("", -1)->useLable("name", "id")->sort(true)->get();
        $fieldMaker->makeField_Select('id', 'name', -1, 'number', $options);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Priority->dbName(), __("thisApp.Priority"));
        $options = DropdownListCreater::makeByArray(TicketPrioritiesEnum::translatedArray())
            ->prepend("", "")->useLable("name", "key")->get();
        $fieldMaker->makeField_Select('key', 'name', "", 'string', $options);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Subject->dbName(), __('general.Subject'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "300");
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TimestampsEnum::CreatedAt->dbName(), __('general.CreatedAt'));
        $fieldMaker->makeField_DateRange();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Status->dbName(), __("general.Status"));
        $options = DropdownListCreater::makeByArray(TicketsStatusEnum::translatedArray())
            ->prepend("", "")->useLable("name", "key")->get();
        $fieldMaker->makeField_Select('key', 'name', "", 'string', $options);
        $attr = $attributes[TableEnum::Status->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value != '';}", trans('validation.required', ['attribute' => $attr]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('personnel_username', __('thisApp.Responder'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        if ($user->can(PermissionAbilityEnum::update->name, Ticket::class)) {

            $fieldMaker = new jsGrid_FieldMaker('answering_btn', __('thisApp.Buttons.Answering'));
            $fieldMaker->makeField_Text();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_ShowOnly);
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
            $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::PrivateNote->dbName(), __('thisApp.PrivateNote'));
        $fieldMaker->makeField_Textarea();
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "300");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, null);
        $fieldMaker->makeField_Control();
        $jsGrid_Controller->putField($fieldMaker);


        $data = [
            config('hhh_config.keywords.jsGridJavaData')    =>  $jsGrid_Controller->create(),
        ];


        return view('hhh.BackOffice.pages.Tickets.AllTickets.index', $data);
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
     *
     * @param \App\Http\Requests\BackOffice\Tickets\TicketRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function store(TicketRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\BackOffice\Tickets\TicketRequest $request
     * @param \App\Models\BackOffice\Tickets\Ticket $ticket
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(TicketRequest $request, Ticket $ticket): JsonResponse
    {

        try {
            $statusCol = TableEnum::Status->dbName();

            $item = Ticket::find($request->input(TableEnum::Id->dbName()));

            $item->fill($request->all());

            if ($item->isDirty($statusCol)) {

                $personnel = User::authUser();
                $item[TableEnum::ResponderId->dbName()] = $personnel->id;
                $request->merge(['personnel_username' => $personnel[UsersTableEnum::Username->dbName()]]);

                if ($item->isDirty($statusCol))
                    TicketsStatusEnum::notifyTicketStatus($item);
            }

            $item->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new TicketResource($request->input()), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\BackOffice\Tickets\TicketRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(TicketRequest $request): JsonResponse
    {
        if ($item = Ticket::find($request->input(TableEnum::Id->dbName()))) {

            $this->destroyTicketMessages($item);

            $item->delete();

            return JsonResponseHelper::successResponse(null, trans('general.ItemSuccessfullyRemoved'));
        }

        return JsonResponseHelper::errorResponse(null, trans('general.NotFoundItem'), HttpResponseStatusCode::BadRequest->value);
    }

    /**
     * Destroy ticket messages
     *
     * @param  mixed $ticket
     * @return void
     */
    private function destroyTicketMessages(Ticket $ticket): void
    {
        // Image messages
        $ticketMessages = $ticket->ticketMessages()
            ->Images()
            ->get();

        /** @var TicketMessage $ticketMessage */
        foreach ($ticketMessages as $ticketMessage) {

            $fileAssistant = $ticketMessage->getPhotoFileAssistant(false);
            $fileAssistant->deleteFile();
        }
    }

    /**
     * Return the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function apiIndex(Request $request)
    {

        $this->authorize(PermissionAbilityEnum::viewAny->name, Ticket::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new TicketsCollection(
            Ticket::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }

}
