<?php

namespace App\Http\Controllers\BackOffice\Tickets;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\TicketMessagesTableEnum;
use App\Enums\Database\Tables\TicketsTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Resources\ImageConfigEnum;
use App\Enums\Routes\AdminPublicRoutesEnum;
use App\Enums\Tickets\TicketMessageTypesEnum;
use App\Enums\Tickets\TicketsStatusEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\FileAssistant;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\general\php\LogCreator;
use App\Http\Controllers\SuperClasses\SuperController;
use App\Models\BackOffice\Tickets\Ticket;
use App\Models\BackOffice\Tickets\TicketMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class TicketMessengerController extends SuperController
{
    /**
     * NOTICE:
     *
     * The "App\Http\Controllers\Site\Tickets\ClientTicketMessengerController"
     * class extened from this class
     */

    // payload keys
    protected const PAYLOAD_KEY = "payload";
    protected const USER_ID_KEY = "userId";
    protected const TICKET_ID_KEY = "ticketId";

    protected ?User $user;
    protected ?Ticket $ticket;

    /**
     * Display a listing of the resource.
     */
    public function index(Ticket $ticket)
    {
        Gate::authorize('answer-Tickets', $ticket);

        $payload = $this->makePayload($ticket);

        if (!is_string($payload))
            return $payload;

        $data = [
            self::PAYLOAD_KEY => $payload,
            'ticket' => $ticket,
            'ticketStatusCollection' => TicketsStatusEnum::translatedArray(),
        ];

        return view('hhh.BackOffice.pages.Tickets.TicketMessenger.index', $data);
    }

    /**
     * Get initial data
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInitialData(Request $request): JsonResponse
    {
        $payloadExtractionResult =  $this->extractPayloadData($request);
        if (!is_null($payloadExtractionResult))
            return $payloadExtractionResult;

        $ticketImagePath = (new FileAssistant(ImageConfigEnum::TicketMessage))->getUrl();
        $ticketImagePath = Str::of($ticketImagePath)->finish('/')->toString();

        $chatbotUserInputImagePath = (new FileAssistant(ImageConfigEnum::ChatbotUserInputImage))->getUrl();
        $chatbotUserInputImagePath = Str::of($chatbotUserInputImagePath)->finish('/')->toString();

        $data = [
            'userId' => $this->user->id,
            'usersProfiles' => $this->getTicketUsersProfilesData(),

            'imagesBasePath' => [
                'ticketImage' => $ticketImagePath,
                'chatbotUserInputImage' => $chatbotUserInputImagePath,
            ],
        ];

        return JsonResponseHelper::successResponse($this->makeResponseData($data), 'Success');
    }

    /**
     * Get previous messages
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPreviousMessages(Request $request): JsonResponse
    {
        $payloadExtractionResult =  $this->extractPayloadData($request);
        if (!is_null($payloadExtractionResult))
            return $payloadExtractionResult;

        try {

            $data = [
                'messages' => $this->fetchLastMessages($request->input('lastMessageId')),
            ];

            return JsonResponseHelper::successResponse($this->makeResponseData($data), 'Success');
        } catch (\Throwable $th) {

            $errorMessage = sprintf(
                "Request: %s\nError: %s",
                json_encode($request->all()),
                $th->getMessage()
            );
            LogCreator::createLogError(get_class(), __FUNCTION__, $errorMessage);

            return JsonResponseHelper::errorResponse('error.UnknownError', __('error.UnknownError'));
        }

        return JsonResponseHelper::errorResponse('error.UnknownError', __('error.UnknownError'));
    }

    /**
     * Register log message
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfileData(Request $request): JsonResponse
    {
        $payloadExtractionResult =  $this->extractPayloadData($request);
        if (!is_null($payloadExtractionResult))
            return $payloadExtractionResult;

        $userId = $request->input('userId');

        if ($user = User::find($userId)) {

            $data = $this->getTicketUsersProfilesData($userId);

            return JsonResponseHelper::successResponse($this->makeResponseData($data), 'Success');
        }

        return JsonResponseHelper::errorResponse('error.UnknownError', __('error.UnknownError'));
    }

    /**
     * Store new message
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function newMessage(Request $request): JsonResponse
    {
        $payloadExtractionResult =  $this->extractPayloadData($request);
        if (!is_null($payloadExtractionResult))
            return $payloadExtractionResult;

        $message = $request->input('message');

        try {

            $ticket = $this->ticket;

            if (!$this->canUserSendMessage())
                return JsonResponseHelper::errorResponse('thisApp.Errors.TicketMessenger.ticketClosed', __('thisApp.Errors.TicketMessenger.ticketClosed'), HttpResponseStatusCode::BadRequest->value, $this->makeResponseData(null));


            $messageType = TicketMessageTypesEnum::Text->name;
            $message = strip_tags($request->input('message'));

            $ticketMessage = new TicketMessage();

            /************** File ******************/
            $attachedFileKey = "attachedFile";

            if ($request->hasFile($attachedFileKey)) {

                // Valivate uploaded image
                $imageConfig = ImageConfigEnum::TicketMessage;

                $imageRules = [
                    'image',
                    "mimes:" . $imageConfig->mimes(),
                    sprintf("dimensions:min_width=%s,min_height=%s", $imageConfig->minWidth(), $imageConfig->minHeight()),
                    sprintf("dimensions:max_width=%s,max_height=%s", $imageConfig->maxWidth(), $imageConfig->maxHeight()),
                    "min:" . $imageConfig->minSize(),
                    "max:" . $imageConfig->maxSize(),
                ];
                $attributes = [
                    $attachedFileKey => __('thisApp.AttachedFile')
                ];
                $validator = Validator::make($request->only($attachedFileKey), [$attachedFileKey => $imageRules], [], $attributes)->stopOnFirstFailure(true);

                if ($validator->fails()) {
                    $errors = $validator->errors()->toArray()[$attachedFileKey];
                    return JsonResponseHelper::errorResponse('Validation.Failed', $errors, HttpResponseStatusCode::PreconditionFailed->value, $this->makeResponseData(null));
                }

                // Store uploaded image
                $messageType = TicketMessageTypesEnum::TicketImage->name;

                $fileAssistant = $ticketMessage->getPhotoFileAssistant(false);
                $storedFileName = $fileAssistant->storeUploadedFile($request, $attachedFileKey);

                $message = $storedFileName;
            }
            /************** File END ******************/

            if (!empty($message)) {

                $ticketMessage->forceFill([
                    TicketMessagesTableEnum::UserId->dbName()   => $request->user()->id,
                    TicketMessagesTableEnum::TicketId->dbName() => $ticket->id,
                    TicketMessagesTableEnum::Type->dbName()     => $messageType,
                    TicketMessagesTableEnum::Content->dbName()  => $message,
                ]);

                if ($ticketMessage->save()) {

                    if ($this->user->isClient()) {
                        $this->ticket[TicketsTableEnum::Status->dbName()] = TicketsStatusEnum::ClientReplied->name;
                        $this->ticket->save();
                    }
                }
            }

            $data = [
                'messages' => $this->fetchLastMessages($request->input('lastMessageId')),
            ];

            return JsonResponseHelper::successResponse($this->makeResponseData($data), 'Success');
        } catch (\Throwable $th) {

            $errorMessage = sprintf(
                "Request: %s\nError: %s",
                json_encode($request->all()),
                $th->getMessage()
            );
            LogCreator::createLogError(get_class(), __FUNCTION__, $errorMessage, "New Message storing exception.");
        }

        return JsonResponseHelper::errorResponse('error.UnknownError', __('error.UnknownError'));
    }

    /**
     * Change ticket status
     *
     * NOTE:
     * This is working just for personnel
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeTicketStatus(Request $request): JsonResponse
    {
        $payloadExtractionResult =  $this->extractPayloadData($request);
        if (!is_null($payloadExtractionResult))
            return $payloadExtractionResult;

        $accessDenied = JsonResponseHelper::errorResponse('thisApp.Errors.TicketMessenger.accessDenied', __('thisApp.Errors.TicketMessenger.accessDenied'), HttpResponseStatusCode::Forbidden->value);

        $user = $this->user;

        if (!$user->isPersonnel())
            return $accessDenied;

        if (Gate::denies('answer-Tickets', $this->ticket))
            return $accessDenied;

        $status = $request->input('status');

        if (!TicketsStatusEnum::hasName($status))
            return $accessDenied;

        $statusCol = TicketsTableEnum::Status->dbName();
        $responderIdCol = TicketsTableEnum::ResponderId->dbName();

        $lastStatus = $this->ticket->$statusCol;
        $this->ticket[$statusCol] = $status;
        $this->ticket[$responderIdCol] = $user->id;
        $this->ticket->save();

        TicketsStatusEnum::notifyTicketStatus($this->ticket);

        $data = [
            'refreshPage' => $lastStatus == TicketsStatusEnum::Closed->name,
        ];
        return JsonResponseHelper::successResponse(
            $this->makeResponseData($data),
            __('thisApp.AdminPages.Tickets.TicketMessenger.TicketStatusChanged', ['newStatus' => TicketsStatusEnum::getCase($status)->translate()])
        );
    }

    /**
     * Submit sidebar form data
     *
     * NOTE:
     * This is working just for personnel
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitForm(Request $request): JsonResponse
    {
        $payloadExtractionResult =  $this->extractPayloadData($request);
        if (!is_null($payloadExtractionResult))
            return $payloadExtractionResult;

        $accessDenied = JsonResponseHelper::errorResponse('thisApp.Errors.TicketMessenger.accessDenied', __('thisApp.Errors.TicketMessenger.accessDenied'), HttpResponseStatusCode::Forbidden->value);

        $user = $this->user;

        if (!$user->isPersonnel())
            return $accessDenied;

        if (Gate::denies('answer-Tickets', $this->ticket))
            return $accessDenied;

        $privateNote = $request->input('privateNote');

        $privateNoteCol = TicketsTableEnum::PrivateNote->dbName();

        $this->ticket[$privateNoteCol] = $privateNote;
        $this->ticket->save();

        $data = [];
        return JsonResponseHelper::successResponse(
            $this->makeResponseData($data),
            __('thisApp.AdminPages.Tickets.TicketMessenger.FormSaved')
        );
    }

    /**
     * Register log message
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerLog(Request $request): JsonResponse
    {
        $payloadExtractionResult =  $this->extractPayloadData($request);

        if (is_null($payloadExtractionResult)) {

            $type = $request->input('type');
            $message = $request->input('message');

            if (!empty($message)) {

                LogCreator::createLog(__CLASS__, __FUNCTION__, $type, $message, 'Ticket Messenger');
            }
        }

        $data = [];
        return JsonResponseHelper::successResponse($data);
    }

    /**
     * Make user hash
     *
     * @param \App\Models\BackOffice\Tickets\Ticket $ticket
     * @param  \App\Models\User|null $user
     * @return string|Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    protected function makePayload(Ticket $ticket, ?User $user = null): string|RedirectResponse
    {
        if (is_null($ticket))
            return redirect()->back();

        if (is_null($user))
            $user = auth()->user();

        // The user was not identified
        if (is_null($user))
            return redirect(AdminPublicRoutesEnum::Login->route());

        $idCol = "id";

        $payloadData = [
            self::USER_ID_KEY => $user->$idCol,
            self::TICKET_ID_KEY => $ticket[TicketsTableEnum::Id->dbName()],
        ];

        $payload = Crypt::encrypt(json_encode($payloadData));

        return $payload;
    }

    /**
     * Extract payload data
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function extractPayloadData(Request $request): JsonResponse|null
    {

        try {
            $this->user = null;
            $this->ticket = null;

            $accessDenied = JsonResponseHelper::errorResponse('thisApp.Errors.TicketMessenger.accessDenied', __('thisApp.Errors.TicketMessenger.accessDenied'), HttpResponseStatusCode::Forbidden->value, ['refreshPage' => true]);

            if (!$request->has(self::PAYLOAD_KEY))
                return $accessDenied;

            $payload = $request->input(self::PAYLOAD_KEY);
            if (empty($payload))
                return $accessDenied;

            $payloadData = json_decode(Crypt::decrypt($payload), true);

            if (isset($payloadData[self::USER_ID_KEY])) {

                if (User::authUser()->id != $payloadData[self::USER_ID_KEY])
                    return $accessDenied;

                $this->user = User::find($payloadData[self::USER_ID_KEY]);

                if (is_null($this->user))
                    return $accessDenied;

                if (!$this->canUserViewMessenger())
                    return $accessDenied;
            } else
                return $accessDenied;

            if (isset($payloadData[self::TICKET_ID_KEY])) {
                $this->ticket = Ticket::find($payloadData[self::TICKET_ID_KEY]);

                if (is_null($this->ticket))
                    return $accessDenied;
            } else
                return $accessDenied;
        } catch (\Throwable $th) {

            $errorMessage = sprintf(
                "Request: %s\nError: %s",
                json_encode($request->all()),
                $th->getMessage()
            );
            LogCreator::createLogAlert(get_class(), __FUNCTION__, $errorMessage, "Payload Error");
        }

        return null;
    }

    /**
     * Attach default data and make final response data
     *
     * @param  ?array $data
     * @return array
     */
    protected function makeResponseData(?array $data): array
    {
        $lastMessageId = $this->ticket->ticketMessages()
            ->orderBy(TicketMessagesTableEnum::Id->dbName(), 'desc')
            ->orderBy(TimestampsEnum::CreatedAt->dbName(), 'desc')
            ->first();

        $defaultData = [
            'csrfToken'         => csrf_token(),
            'debugMode'         => (bool) config('app.debug'),
            'canSendMessage'    => $this->canUserSendMessage(),
            'lastMessageId'     => is_null($lastMessageId) ? null : $lastMessageId->id,
            'synchronize'       => (bool) ($this->ticket[TicketsTableEnum::Status->dbName()] != TicketsStatusEnum::Closed->name),
        ];

        return (is_null($data)) ? $defaultData : array_merge($defaultData, $data);
    }

    /**
     * Get ticket users profiles data
     *
     * @param  ?int $userId If you need all users involved in this ticket, leave this parameter null
     * @return array
     */
    protected function getTicketUsersProfilesData(?int $userId = null): array
    {
        if (!is_null($userId)) {
            // Single user data

            if ($user = User::find($userId)) {

                $data = [
                    'dispalyName' => $user[UsersTableEnum::DisplayName->dbName()],
                    'profileImage' => $user[UsersTableEnum::PhotoUrl->dbName()],
                ];

                return $data;
            }
        } else {
            // All users involved in this ticket

            $userIdCol = TicketMessagesTableEnum::UserId->dbName();

            $userIds = $this->ticket->ticketMessages()
                ->select($userIdCol)
                ->groupby($userIdCol)->distinct()
                ->get()
                ->pluck($userIdCol)
                ->toArray();

            $userProfiles = [];

            foreach ($userIds as $userId) {
                $userProfiles[$userId] = $this->getTicketUsersProfilesData($userId);
            }

            return $userProfiles;
        }

        return [];
    }

    /**
     * Check if user can send message
     *
     * @return bool
     */
    protected function canUserSendMessage(): bool
    {
        return (bool) ($this->ticket[TicketsTableEnum::Status->dbName()] !== TicketsStatusEnum::Closed->name);
    }

    /**
     * Check if user can view messenger
     *
     * @return bool
     */
    protected function canUserViewMessenger(): bool
    {
        return Gate::allows('answer-Tickets', $this->ticket);
    }

    /**
     * Fetch last ticket messages
     *
     * @param  ?string $lastTicketMessageId
     * @return null|\Illuminate\Database\Eloquent\Collection
     */
    protected function fetchLastMessages(?string $lastTicketMessageId): ?Collection
    {

        $lastTicketMessage = TicketMessage::where(TicketMessagesTableEnum::Id->dbName(), $lastTicketMessageId)->first();

        if (!is_null($lastTicketMessage)) {

            $messages = $this->ticket->ticketMessages()
                ->where(TicketMessagesTableEnum::Id->dbName(), '>', $lastTicketMessageId)
                ->get();
        } else {
            $messages = $this->ticket->ticketMessages;
        }

        return $messages;
    }
}
