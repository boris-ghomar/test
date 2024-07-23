<?php

namespace App\Http\Controllers\Site\Chatbot;

use App\Enums\Chatbot\ChatbotStepActions\ChatbotActionActionEnum;
use App\Enums\Chatbot\ChatbotStepActions\ChatbotActionTypesEnum;
use App\Enums\Chatbot\ChatbotStepActions\ChatbotFilterActionEnum;
use App\Enums\Chatbot\ChatbotStepActions\ChatbotResponseActionEnum;
use App\Enums\Chatbot\ChatbotStepActions\ChatbotUserInputActionEnum;
use App\Enums\Chatbot\ChatbotStepActions\ChatbotUserInputTypesEnum;
use App\Enums\Chatbot\ChatbotStepTypesEnum;
use App\Enums\Chatbot\Messenger\ChatbotChatStatusEnum;
use App\Enums\Chatbot\Messenger\ChatbotMessageTypesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\ChatbotChatsTableEnum;
use App\Enums\Database\Tables\ChatbotMessagesTableEnum;
use App\Enums\Database\Tables\ChatbotsTableEnum;
use App\Enums\Database\Tables\ChatbotStepsTableEnum;
use App\Enums\Database\Tables\TicketsTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Resources\ImageConfigEnum;
use App\Enums\Routes\SitePublicRoutesEnum;
use App\Enums\Tickets\TicketableTypesEnum;
use App\Enums\Tickets\TicketsStatusEnum;
use App\HHH_Library\general\php\CarbonTimeDiffForHuman;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\FileAssistant;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\general\php\LogCreator;
use App\Http\Controllers\SuperClasses\SuperController;
use App\Models\BackOffice\Chatbot\Chatbot;
use App\Models\BackOffice\Chatbot\ChatbotChat;
use App\Models\BackOffice\Chatbot\ChatbotStep;
use App\Models\BackOffice\ClientsManagement\UserBetconstruct;
use App\Models\BackOffice\Tickets\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ChatbotMessengerController extends SuperController
{
    // payload keys
    private const PAYLOAD_KEY = "payload";
    private const USER_ID_KEY = "userId";
    private const CHAT_ID_KEY = "chatId";

    private ?UserBetconstruct $client;
    private ?ChatbotChat $chatbotChat;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payload = $this->makePayload();

        if (!is_string($payload))
            return $payload;

        $data = [
            self::PAYLOAD_KEY => $payload,
        ];

        return view('hhh.Site.pages.Chatbot.ChatbotMessenger.index', $data);
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

        $chatbotImageResponsePath = (new FileAssistant(ImageConfigEnum::ChatbotImageResponse))->getUrl();
        $chatbotImageResponsePath = Str::of($chatbotImageResponsePath)->finish('/')->toString();

        $chatbotUserInputImagePath = (new FileAssistant(ImageConfigEnum::ChatbotUserInputImage))->getUrl();
        $chatbotUserInputImagePath = Str::of($chatbotUserInputImagePath)->finish('/')->toString();

        /** @var  Chatbot $chatbot*/
        $chatbot = $this->chatbotChat->chatbot;

        if (is_null($this->client)) {
            // Guest User

            $profileFileConfig = User::getPhotoFileConfig();
            $fileAssistant = new FileAssistant($profileFileConfig);
            $fileAssistant->setPath($profileFileConfig->defaultPath());
            $fileAssistant->setName($profileFileConfig->defaultImage());

            $clientDisplayName = __('thisApp.GuestUser');
            $clientProfileImage = $fileAssistant->getUrl();
        } else {
            // Logged User
            $clientDisplayName = $this->client[UsersTableEnum::DisplayName->dbName()];
            $clientProfileImage = $this->client[UsersTableEnum::PhotoUrl->dbName()];
        }

        $data = [

            'imagesBasePath' => [
                'imageResponse' => $chatbotImageResponsePath,
                'userInputImage' => $chatbotUserInputImagePath,
            ],

            'chatbotProfile' => [
                'dispalyName' => $chatbot[ChatbotsTableEnum::Name->dbName()],
                'profileImage' => $chatbot[ChatbotsTableEnum::PhotoUrl->dbName()],
            ],

            'clientProfile' => [
                'dispalyName' => $clientDisplayName,
                'profileImage' => $clientProfileImage,
            ],
        ];

        return JsonResponseHelper::successResponse($data, 'Success');
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

        $chatbotChat = $this->chatbotChat;

        try {

            $messages = $chatbotChat->chatbotMessages;

            $data = [
                'messages' => $messages,
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
     * Get next step message
     *
     * @param  \Illuminate\Http\Request $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function getNextStepMessage(Request $request): JsonResponse
    {
        $payloadExtractionResult =  $this->extractPayloadData($request);
        if (!is_null($payloadExtractionResult))
            return $payloadExtractionResult;

        return $this->makeNextStepMessage();
    }

    /**
     * Get custom step message
     *
     * @param  \Illuminate\Http\Request $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function goToStep(Request $request): JsonResponse
    {
        $payloadExtractionResult =  $this->extractPayloadData($request);
        if (!is_null($payloadExtractionResult))
            return $payloadExtractionResult;

        try {

            $targetStepId = Crypt::decrypt($request->input('stepId'));
        } catch (\Throwable $th) {
            $targetStepId = null;
        }

        $chatbotStep = ChatbotStep::where(ChatbotStepsTableEnum::Id->dbName(), $targetStepId)
            ->where(ChatbotStepsTableEnum::ChatbotId->dbName(), $this->chatbotChat[ChatbotChatsTableEnum::ChatbotId->dbName()])
            ->first();

        $stepMessage = $this->makeStepMessage($chatbotStep);

        if ($stepMessage !== false)
            return $stepMessage;

        // There is no message
        return JsonResponseHelper::successResponse(null, 'Success');
    }

    /**
     * Submit user input
     *
     * @param  \Illuminate\Http\Request $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function submitUserInput(Request $request): JsonResponse
    {
        $payloadExtractionResult =  $this->extractPayloadData($request);
        if (!is_null($payloadExtractionResult))
            return $payloadExtractionResult;

        if (is_null($this->client))
            return JsonResponseHelper::errorResponse('thisApp.Errors.ChatbotMessenger.loginRequired', __('thisApp.Errors.ChatbotMessenger.loginRequired'), HttpResponseStatusCode::Forbidden->value);

        $accessDenied = JsonResponseHelper::errorResponse('thisApp.Errors.ChatbotMessenger.accessDenied', __('thisApp.Errors.ChatbotMessenger.accessDenied'), HttpResponseStatusCode::Forbidden->value);
        $accessDeniedWithRefresh = JsonResponseHelper::errorResponse('thisApp.Errors.ChatbotMessenger.accessDenied', __('thisApp.Errors.ChatbotMessenger.accessDenied'), HttpResponseStatusCode::Forbidden->value, $this->makeErrorActionData(true));

        try {

            $stepId = Crypt::decrypt($request->input('stepId'));
        } catch (\Throwable $th) {
            $stepId = null;
            return $accessDenied;
        }

        if ($chatbotStep = ChatbotStep::find($stepId)) {

            $validation = $this->validateUserInput($request, $chatbotStep);

            if ($validation !== true)
                return $validation;

            $lastMessage = $this->chatbotChat->getLastMessage();

            $lastMessageContent = $lastMessage[ChatbotMessagesTableEnum::Content->dbName()];
            $messageType = $lastMessageContent[ChatbotUserInputTypesEnum::KEY_TYPE];

            // Prepare user input
            $userInput = null;
            $userInputKey = 'userInput';
            if ($messageType == ChatbotUserInputTypesEnum::Image->name) {

                if ($request->hasFile($userInputKey)) {

                    $lastFile = $lastMessage->getPhotoFileAssistant(false);
                    $storedFileName = $lastFile->storeUploadedFile($request, $userInputKey);

                    if ($storedFileName != null) {
                        $userInput = $storedFileName;
                    } else {
                        return JsonResponseHelper::errorResponse('thisApp.Errors.ChatbotMessenger.imageNotUploaded', __('thisApp.Errors.ChatbotMessenger.imageNotUploaded'), HttpResponseStatusCode::FailedDependency->value);
                    }
                }
            } else {

                $userInput = $request->input($userInputKey);
                $userInput = Str::of($userInput)->trim()->stripTags()->toString();
                $request->merge([$userInputKey => $userInput]);
            }

            $lastMessageContent[ChatbotUserInputTypesEnum::KEY_USER_ANSWER] = $userInput;
            $lastMessage[ChatbotMessagesTableEnum::Content->dbName()] = $lastMessageContent;
            $lastMessage[ChatbotMessagesTableEnum::IsPassed->dbName()] = true;
            $lastMessage->save();

            return JsonResponseHelper::successResponse($this->makeResponseData($lastMessage->toArray()), 'Success');
        } else {
            $this->changeChatStatus(ChatbotChatStatusEnum::Closed);
            return $accessDeniedWithRefresh;
        }

        $errorMessage = sprintf(
            "Unknown error!\nRequest: %s\nChatbot Chat ID: %s\nChatbot Step ID: %s\nUser ID: %s",
            json_encode($request->all()),
            $this->chatbotChat[ChatbotChatsTableEnum::Id->dbName()],
            $chatbotStep[ChatbotStepsTableEnum::Id->dbName()],
            $this->client[UsersTableEnum::Id->dbName()]
        );
        LogCreator::createLogError(get_class(), __FUNCTION__, $errorMessage);

        return JsonResponseHelper::errorResponse('error.UnknownError', __('error.UnknownError'));
    }

    /**
     * Close chatbot chat
     *
     * @param  \Illuminate\Http\Request $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function closeChat(Request $request): JsonResponse
    {
        $payloadExtractionResult =  $this->extractPayloadData($request);
        if (!is_null($payloadExtractionResult))
            return $payloadExtractionResult;

        $this->changeChatStatus(ChatbotChatStatusEnum::Closed);

        $data = [
            'redirectUrl' => SitePublicRoutesEnum::MainPage->url(),
        ];

        return JsonResponseHelper::successResponse($data, 'Success');
    }

    /**
     * Make user hash
     *
     * @param  \App\Models\User|null $user
     * @return string|Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    private function makePayload(?User $user = null): string|RedirectResponse
    {
        $idCol = "id";

        /** @var User $user */
        if (is_null($user)) {
            // try to use auth user
            $user = auth()->user();
        }

        $userId = is_null($user) ? null :  $user->$idCol;

        // There is no active chatbot for client
        $clientActiveChat = $this->getClientActiveChat($user);
        if (is_null($clientActiveChat)) {

            $previousUrl = url()->previous();

            if ($previousUrl == SitePublicRoutesEnum::Support_Chatbot->url()) {
                // Avoid the redirection loop
                return redirect(SitePublicRoutesEnum::MainPage->route());
            } else if (Str::of($previousUrl)->startsWith(config('app.url'))) {
                // The previous URL belongs to the site, so redirect back
                return redirect()->back();
            }

            /**
             * The previous URL does not belong to the site,
             * so it should be redirected to the main page of the site
             * so that the user does not leave the site.
             *
             * On the other hand, the possibility of a redirect loop to
             * the previous and current site is prevented.
             */
            return redirect(SitePublicRoutesEnum::MainPage->route());
        }


        $payloadData = [
            self::USER_ID_KEY => $userId,
            self::CHAT_ID_KEY => $clientActiveChat->$idCol,
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
    private function extractPayloadData(Request $request): JsonResponse|null
    {

        try {
            $this->client = null;
            $this->chatbotChat = null;

            $accessDenied = JsonResponseHelper::errorResponse('thisApp.Errors.ChatbotMessenger.accessDenied', __('thisApp.Errors.ChatbotMessenger.accessDenied'), HttpResponseStatusCode::Forbidden->value);
            $accessDeniedWithRefresh = JsonResponseHelper::errorResponse('thisApp.Errors.ChatbotMessenger.accessDenied', __('thisApp.Errors.ChatbotMessenger.accessDenied'), HttpResponseStatusCode::Forbidden->value, $this->makeErrorActionData(true));

            if (!$request->has(self::PAYLOAD_KEY))
                return $accessDenied;

            $payload = $request->input(self::PAYLOAD_KEY);
            if (empty($payload))
                return $accessDenied;

            $payloadData = json_decode(Crypt::decrypt($payload), true);

            if (isset($payloadData[self::USER_ID_KEY])) {
                $user = UserBetconstruct::find($payloadData[self::USER_ID_KEY]);

                if (!is_null($user)) {

                    if ($user->betconstructClient) {
                        // User has betconstruct extra data
                        $this->client = $user;
                    } else {
                        Auth::logout();
                        return $accessDeniedWithRefresh;
                    }
                }
            }

            if (isset($payloadData[self::CHAT_ID_KEY])) {
                $this->chatbotChat = ChatbotChat::find($payloadData[self::CHAT_ID_KEY]);

                if (is_null($this->chatbotChat))
                    return $accessDenied;

                $chatValidation = $this->isChatValid($this->chatbotChat);
                if (!is_null($chatValidation))
                    return $chatValidation;
            }
        } catch (\Throwable $th) {

            $errorMessage = sprintf(
                "Request: %s\nError: %s",
                json_encode($request->all()),
                $th->getMessage()
            );
            LogCreator::createLogAlert(get_class(), __FUNCTION__, $errorMessage);
        }

        return null;
    }

    /**
     * Get client active chat
     *
     * @param  \App\Models\User|null $user
     * @return \App\Models\BackOffice\Chatbot\ChatbotChat|null
     */
    private function getClientActiveChat(?User $user): ChatbotChat|null
    {

        if (is_null($user)) {
            // Guest user

            $sessionKey = "ChatbotChatId";
            $chatbotChatId = Session::get($sessionKey);

            // Try to load previous chatbot chat if exists
            $activeChat = ChatbotChat::where(ChatbotChatsTableEnum::Id->dbName(), $chatbotChatId)
                ->where(ChatbotChatsTableEnum::Status->dbName(), '!=', ChatbotChatStatusEnum::Closed->name)
                ->orderBy(ChatbotChatsTableEnum::Id->dbName(), 'desc')
                ->first();

            if (!is_null($activeChat))
                if ($activeChat->chatbot[ChatbotsTableEnum::IsActive->dbName()])
                    return $activeChat;

            $activeChatbot = Chatbot::where(ChatbotsTableEnum::IsActive->dbName(), 1)
                ->orderBy(ChatbotsTableEnum::Id->dbName(), 'asc')
                ->first();

            if (!is_null($activeChatbot)) {

                $activeChat = new ChatbotChat([
                    ChatbotChatsTableEnum::UserId->dbName() => null,
                    ChatbotChatsTableEnum::ChatbotId->dbName() => $activeChatbot->id,
                    ChatbotChatsTableEnum::Status->dbName() => ChatbotChatStatusEnum::Active->name,
                ]);

                $activeChat->save();

                Session::put($sessionKey, $activeChat->id);

                return $activeChat;
            }

            return null;
        } else {
            // logged user

            if ($user->isPersonnel())
                return null;

            $activeChat = $user->chatbotChats()
                ->where(ChatbotChatsTableEnum::Status->dbName(), '!=', ChatbotChatStatusEnum::Closed->name)
                ->orderBy(ChatbotChatsTableEnum::Id->dbName(), 'desc')
                ->first();

            if (!is_null($activeChat))
                return $activeChat;

            $responsiveChatbotId = $user->getResponsiveChatbotId();
            if ($responsiveChatbotId === false)
                return null;

            $activeChat = new ChatbotChat([
                ChatbotChatsTableEnum::UserId->dbName() => $user[UsersTableEnum::Id->dbName()],
                ChatbotChatsTableEnum::ChatbotId->dbName() => $responsiveChatbotId,
                ChatbotChatsTableEnum::Status->dbName() => ChatbotChatStatusEnum::Active->name,
            ]);

            $activeChat->save();

            return $activeChat;
        }

        return null;
    }

    /**
     * Check if chat is valid
     *
     * @param  \App\Models\BackOffice\Chatbot\ChatbotChat|null $chatbotChat
     * @return \Illuminate\Http\JsonResponse|null
     */
    private function isChatValid(?ChatbotChat $chatbotChat): JsonResponse|null
    {
        if (is_null($chatbotChat))
            return JsonResponseHelper::errorResponse('thisApp.Errors.ChatbotMessenger.activeChatbotNotFound', __('thisApp.Errors.ChatbotMessenger.activeChatbotNotFound'), HttpResponseStatusCode::NotFound->value);

        if (!is_null($this->client))
            if ($chatbotChat[ChatbotChatsTableEnum::UserId->dbName()] != $this->client[UsersTableEnum::Id->dbName()])
                return JsonResponseHelper::errorResponse('thisApp.Errors.ChatbotMessenger.accessDenied', __('thisApp.Errors.ChatbotMessenger.accessDenied'), HttpResponseStatusCode::Forbidden->value, $this->makeErrorActionData(true));

        if (is_null($this->client)) {
            // Just for Guest users, other user maybe is chatbot tester
            if (!$chatbotChat->chatbot[ChatbotsTableEnum::IsActive->dbName()]) {
                $this->changeChatStatus(ChatbotChatStatusEnum::Closed);
            }
        }

        if ($chatbotChat[ChatbotChatsTableEnum::Status->dbName()] == ChatbotChatStatusEnum::Closed->name)
            return JsonResponseHelper::errorResponse('thisApp.Errors.ChatbotMessenger.chatClosed', __('thisApp.Errors.ChatbotMessenger.chatClosed'), HttpResponseStatusCode::Forbidden->value, $this->makeErrorActionData(true));

        return null;
    }

    /**
     * Make error action data
     *
     * @param  bool $refreshChatPage
     * @return array
     */
    private function makeErrorActionData(bool $refreshChatPage): array
    {
        return [
            'refreshPage' => $refreshChatPage,
        ];
    }

    /**
     * Attach default data and make final response data
     *
     * @param  ?array $data
     * @return array
     */
    private function makeResponseData(?array $data): array
    {
        $defaultData = [
            'csrfToken' => csrf_token(),
        ];

        return (is_null($data)) ? $defaultData : array_merge($defaultData, $data);
    }

    /**
     * Make next step message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function makeNextStepMessage(): JsonResponse
    {

        $chatbotChat = $this->chatbotChat;

        $lastStepId = 0; // Strat chat
        $isStepPassed = true;
        if ($lastMessage = $chatbotChat->getLastMessage()) {
            // Continue chat
            $lastStepId = $lastMessage[ChatbotMessagesTableEnum::ChatbotStepId->dbName()];
            $isStepPassed = $lastMessage[ChatbotMessagesTableEnum::IsPassed->dbName()];
        }

        if ($isStepPassed) {

            /** @var  Chatbot $chatbot*/
            $chatbot = $chatbotChat->chatbot;

            $childSteps = $chatbot->chatbotSteps()
                ->where(ChatbotStepsTableEnum::ParentId->dbName(), $lastStepId)
                ->orderBy(ChatbotStepsTableEnum::Position->dbName(), 'asc')
                ->get();

            foreach ($childSteps as $chatbotStep) {
                // Checking child steps to find the proper step
                $stepMessage = $this->makeStepMessage($chatbotStep);

                if ($stepMessage !== false)
                    return $stepMessage;
            }
        }

        // There is no message
        return JsonResponseHelper::successResponse(null, 'Success');
    }

    /**
     * Make custom step message
     *
     * @param  \App\Models\BackOffice\Chatbot\ChatbotStep $chatbotStep
     * @return \Illuminate\Http\JsonResponse
     */
    private function makeStepMessage(?ChatbotStep $chatbotStep): JsonResponse|false
    {
        if (!is_null($chatbotStep)) {

            $stepTypeKey = ChatbotStepsTableEnum::Type->dbName();

            $stepAction = $chatbotStep[ChatbotStepsTableEnum::Action->dbName()];

            // BotResponse
            if ($chatbotStep[$stepTypeKey] == ChatbotStepTypesEnum::BotResponse->name) {

                $chatbotMessage = ChatbotResponseActionEnum::makeChatMessage($chatbotStep, $this->chatbotChat);

                if (!is_null($chatbotMessage)) {
                    $chatbotMessage['delay'] = $stepAction[ChatbotResponseActionEnum::Delay->name];
                    return JsonResponseHelper::successResponse($this->makeResponseData($chatbotMessage->toArray()), 'Success');
                }
            }
            // UserInput
            else if ($chatbotStep[$stepTypeKey] == ChatbotStepTypesEnum::UserInput->name) {

                $chatbotMessage = ChatbotUserInputActionEnum::makeChatMessage($chatbotStep, $this->chatbotChat);

                if (!is_null($chatbotMessage)) {
                    return JsonResponseHelper::successResponse($this->makeResponseData($chatbotMessage->toArray()), 'Success');
                }
            }
            // Filter
            else if ($chatbotStep[$stepTypeKey] == ChatbotStepTypesEnum::Filter->name) {

                $chatbotMessage = ChatbotFilterActionEnum::makeChatMessage($chatbotStep, $this->chatbotChat);

                // Filter pass failed
                if ($chatbotMessage === false)
                    return false;

                if (!is_null($chatbotMessage)) {
                    return JsonResponseHelper::successResponse($this->makeResponseData($chatbotMessage->toArray()), 'Success');
                }
            }
            // BotAction
            else if ($chatbotStep[$stepTypeKey] == ChatbotStepTypesEnum::BotAction->name) {

                return $this->runBotAction($chatbotStep);
            }
        }

        // There is no message
        return JsonResponseHelper::successResponse(null, 'Success');
    }

    /**
     * Change chat status
     *
     * @param  \App\Enums\Chatbot\Messenger\ChatbotChatStatusEnum $status
     * @return void
     */
    private function changeChatStatus(ChatbotChatStatusEnum $status): void
    {

        if (!is_null($this->chatbotChat)) {

            $this->chatbotChat[ChatbotChatsTableEnum::Status->dbName()] = $status->name;
            $this->chatbotChat->save();
        }
    }

    /**
     * Validate user input
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\BackOffice\Chatbot\ChatbotStep $chatbotStep
     * @return \Illuminate\Http\JsonResponse|bool
     */
    private function validateUserInput(Request $request, ChatbotStep $chatbotStep): JsonResponse|bool
    {

        if (is_null($this->client))
            return JsonResponseHelper::errorResponse('thisApp.Errors.ChatbotMessenger.loginRequired', __('thisApp.Errors.ChatbotMessenger.loginRequired'), HttpResponseStatusCode::Forbidden->value);

        $accessDenied = JsonResponseHelper::errorResponse('thisApp.Errors.ChatbotMessenger.accessDenied', __('thisApp.Errors.ChatbotMessenger.accessDenied'), HttpResponseStatusCode::Forbidden->value);
        $accessDeniedWithRefresh = JsonResponseHelper::errorResponse('thisApp.Errors.ChatbotMessenger.accessDenied', __('thisApp.Errors.ChatbotMessenger.accessDenied'), HttpResponseStatusCode::Forbidden->value, $this->makeErrorActionData(true));

        // This is not user input step
        if ($chatbotStep[ChatbotStepsTableEnum::Type->dbName()] != ChatbotStepTypesEnum::UserInput->name)
            return $accessDeniedWithRefresh;

        $lastMessage = $this->chatbotChat->getLastMessage();

        // The last message not existss
        if (is_null($lastMessage))
            return $accessDeniedWithRefresh;

        // The last message has been answered and there is no need to reply
        if ($lastMessage[ChatbotMessagesTableEnum::IsPassed->dbName()])
            return $accessDeniedWithRefresh;

        // This input not related to last message of chatbot
        if ($lastMessage[ChatbotMessagesTableEnum::ChatbotStepId->dbName()] != $chatbotStep[ChatbotStepsTableEnum::Id->dbName()])
            return $accessDeniedWithRefresh;

        $userInputKey = 'userInput';
        $messageContent = $lastMessage[ChatbotMessagesTableEnum::Content->dbName()];
        $messageType = $messageContent[ChatbotUserInputTypesEnum::KEY_TYPE];
        $messageData = $messageContent[ChatbotUserInputTypesEnum::KEY_DATA];

        /********** Setup Rules ************/
        $rules = [];

        if ($messageType == ChatbotUserInputTypesEnum::Image->name) {

            if ($request->hasFile($userInputKey)) {

                $userInputImageConfig = ImageConfigEnum::ChatbotUserInputImage;

                $imageRules = [
                    'image',
                    "mimes:" . $userInputImageConfig->mimes(),
                    sprintf("dimensions:min_width=%s,min_height=%s", $userInputImageConfig->minWidth(), $userInputImageConfig->minHeight()),
                    sprintf("dimensions:max_width=%s,max_height=%s", $userInputImageConfig->maxWidth(), $userInputImageConfig->maxHeight()),
                    "min:" . $userInputImageConfig->minSize(),
                    "max:" . $userInputImageConfig->maxSize(),
                ];

                $rules = array_merge($rules, $imageRules);
            } else {
                $request->merge([$userInputKey => null]);
            }
        }

        $key = ChatbotUserInputTypesEnum::KEY_REQUIRED;
        if (isset($messageData[$key])) {

            if (CastEnum::Boolean->cast($messageData[$key]))
                array_push($rules, 'required');
        }

        $key = ChatbotUserInputTypesEnum::KEY_REQUIRED;
        if (isset($messageData[$key])) {

            if (CastEnum::Boolean->cast($messageData[$key]))
                array_push($rules, 'required');
        }

        $key = ChatbotUserInputTypesEnum::KEY_TYPE;
        if ($messageContent[$key] == ChatbotUserInputTypesEnum::Number->name) {
            array_push($rules, 'numeric');
        }

        $key = ChatbotUserInputTypesEnum::KEY_MIN;
        if (isset($messageData[$key])) {

            if (!empty($messageData[$key]))
                array_push($rules, 'min:' . $messageData[$key]);
        }

        $key = ChatbotUserInputTypesEnum::KEY_MAX;
        if (isset($messageData[$key])) {

            if (!empty($messageData[$key]))
                array_push($rules, 'max:' . $messageData[$key]);
        }

        $key = ChatbotUserInputTypesEnum::KEY_MIN_LENGTH;
        if (isset($messageData[$key])) {

            if (!empty($messageData[$key]))
                array_push($rules, 'min:' . $messageData[$key]);
        }

        $key = ChatbotUserInputTypesEnum::KEY_MAX_LENGTH;
        if (isset($messageData[$key])) {

            if (!empty($messageData[$key]))
                array_push($rules, 'max:' . $messageData[$key]);
        }
        /********** Setup Rules END ************/
        $attributes = [
            $userInputKey => sprintf('" %s "', $messageData[ChatbotUserInputTypesEnum::KEY_TITLE]),
        ];
        $validator = Validator::make($request->only($userInputKey), [$userInputKey => $rules], [], $attributes)->stopOnFirstFailure(true);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray()[$userInputKey];
            return JsonResponseHelper::errorResponse('Validation.Failed', $errors, HttpResponseStatusCode::PreconditionFailed->value);
        }

        return true;
    }

    /**
     * Run chatbot action
     *
     * @param  \App\Models\BackOffice\Chatbot\ChatbotStep $chatbotStep
     * @return \Illuminate\Http\JsonResponse
     */
    private function runBotAction(ChatbotStep $chatbotStep): JsonResponse
    {

        $actionTypeKey = ChatbotActionActionEnum::Type->name;
        $data = $chatbotStep[ChatbotStepsTableEnum::Action->dbName()];

        // Action: GoToStep
        if ($data[$actionTypeKey] == ChatbotActionTypesEnum::GoToStep->name) {

            $targetStep = $data[ChatbotActionActionEnum::Data->name][ChatbotActionTypesEnum::KEY_TARGET_STEP];

            $stepMessage = $this->makeStepMessage(ChatbotStep::find($targetStep));

            if ($stepMessage !== false)
                return $stepMessage;
        }
        // Action: End
        else if ($data[$actionTypeKey] == ChatbotActionTypesEnum::End->name) {

            // There is no message
            return JsonResponseHelper::successResponse(null, 'Success');
        }
        // Action: StartTicket
        else if ($data[$actionTypeKey] == ChatbotActionTypesEnum::StartTicket->name) {

            $validateStartTicket = $this->validateStartTicket($chatbotStep);
            if (!is_null($validateStartTicket))
                return $validateStartTicket;

            $chatbotMessage = ChatbotActionActionEnum::makeChatMessage($chatbotStep, $this->chatbotChat);
            if (!is_null($chatbotMessage)) {
                return JsonResponseHelper::successResponse($this->makeResponseData($chatbotMessage->toArray()), 'Success');
            }
        }
        // Action: MakeTicket
        else if ($data[$actionTypeKey] == ChatbotActionTypesEnum::MakeTicket->name) {

            return $this->botActionMakeTicket($chatbotStep);
        }

        // There is no message
        return JsonResponseHelper::successResponse(null, 'Success');
    }

    /**
     * Validate if user can make new ticket
     *
     * @param  \App\Models\BackOffice\Chatbot\ChatbotStep $chatbotStep
     * @return \Illuminate\Http\JsonResponse|null
     */
    private function validateStartTicket(ChatbotStep $chatbotStep): ?JsonResponse
    {
        if (is_null($this->client))
            return JsonResponseHelper::errorResponse('thisApp.Errors.ChatbotMessenger.loginRequired', __('thisApp.Errors.ChatbotMessenger.loginRequired'), HttpResponseStatusCode::Forbidden->value);

        $idCol = "id";
        $actionDataKey = ChatbotActionActionEnum::Data->name;
        $action = $chatbotStep[ChatbotStepsTableEnum::Action->dbName()];
        $actionData = $action[$actionDataKey];

        $hourLimit = $actionData[ChatbotActionTypesEnum::KEY_TICKET_HOUR_LIMIT];
        $numberLimit = $actionData[ChatbotActionTypesEnum::KEY_TICKET_NUMBER_LIMIT];
        $scheduleFaildTargetStepId = $actionData[ChatbotActionTypesEnum::KEY_TICKET_SCHEDULE_FAILED_TARGET_STEP];

        $scheduleFaildTargetChatbotStep = $this->chatbotChat->chatbot->chatbotSteps()
            ->where(ChatbotStepsTableEnum::Id->dbName(), $scheduleFaildTargetStepId)
            ->first();

        if (!empty($scheduleFaildTargetStepId) && is_null($scheduleFaildTargetChatbotStep)) {
            // scheduleFaildTargetStep not found
            $errorMessage = sprintf(
                "Chatbot action[Start Ticket]: The 'scheduleFaildTargetStep' not found.\nChatbot Chat ID: %s\nChatbot 'StartTicket' Step ID: %s\nUser ID: %s ",
                $this->chatbotChat->id,
                $chatbotStep->id,
                User::authUser()->id

            );

            LogCreator::createLogError(get_class(), __FUNCTION__, $errorMessage, "Chatbot: Faild to start ticket.");
        }

        $fallbackStepId = is_null($scheduleFaildTargetChatbotStep) ? null : Crypt::encrypt($scheduleFaildTargetChatbotStep->$idCol);


        // #1 Check same open ticket
        $openTicket = Ticket::where(TicketsTableEnum::OwnerId->dbName(), User::authUser()->id)
            ->where(TicketsTableEnum::TicketableType->dbName(), TicketableTypesEnum::ChatbotStep->name)
            ->where(TicketsTableEnum::TicketableId->dbName(), $chatbotStep->id)
            ->where(TicketsTableEnum::Status->dbName(), '!=', TicketsStatusEnum::Closed->name);

        if ($openTicket->exists()) {

            return JsonResponseHelper::errorResponse(
                'thisApp.Errors.ChatbotMessenger.clientHasSameOpenTicket',
                __('thisApp.Errors.ChatbotMessenger.clientHasSameOpenTicket'),
                HttpResponseStatusCode::TooEarly->value,
                ['GoToStep' => $fallbackStepId]
            );
        }

        // #2 Check ticket schedule limits
        if (empty($hourLimit) || $hourLimit < 1) return null;
        if (empty($numberLimit) || $numberLimit < 1) return null;


        $hourLimitExpires = Carbon::now()->subHours($hourLimit);

        $ticketsInLimitedTime = Ticket::where(TicketsTableEnum::OwnerId->dbName(), User::authUser()->id)
            ->where(TicketsTableEnum::TicketableType->dbName(), TicketableTypesEnum::ChatbotStep->name)
            ->where(TicketsTableEnum::TicketableId->dbName(), $chatbotStep->id)
            ->where(TimestampsEnum::UpdatedAt->dbName(), '>', $hourLimitExpires);

        if ($ticketsInLimitedTime->count() >= $numberLimit) {

            $closestTicketToExpire = $ticketsInLimitedTime->orderBy(TimestampsEnum::UpdatedAt->dbName(), 'asc')->first();
            $closestupdatedAt = $closestTicketToExpire->getRawOriginal(TimestampsEnum::UpdatedAt->dbName());
            $remainingTime = (new CarbonTimeDiffForHuman($hourLimitExpires, $closestupdatedAt))
                ->ignoreSuffixes()
                ->getDiff();

            return JsonResponseHelper::errorResponse(
                'thisApp.Errors.ChatbotMessenger.clientReachedToTicketLimit',
                __('thisApp.Errors.ChatbotMessenger.clientReachedToTicketLimit', [
                    'hourLimit' => $hourLimit,
                    'numberLimit' => $numberLimit,
                    'remainingTime' => $remainingTime,
                ]),
                HttpResponseStatusCode::TooEarly->value,
                ['GoToStep' => $fallbackStepId]
            );
        }



        return null;
    }

    /**
     * Chatbot action: Make Ticket
     *
     * @param  \App\Models\BackOffice\Chatbot\ChatbotStep $chatbotStep
     * @return \Illuminate\Http\JsonResponse
     */
    private function botActionMakeTicket(ChatbotStep $chatbotStep): JsonResponse
    {
        if (is_null($this->client))
            return JsonResponseHelper::errorResponse('thisApp.Errors.ChatbotMessenger.loginRequired', __('thisApp.Errors.ChatbotMessenger.loginRequired'), HttpResponseStatusCode::Forbidden->value);

        $idCol = "id";
        $typeCol = ChatbotMessagesTableEnum::Type->dbName();
        $contentCol = ChatbotMessagesTableEnum::Content->dbName();
        $chatbotStepIdCol = ChatbotMessagesTableEnum::ChatbotStepId->dbName();

        // Get all BotAction Messages
        $botActionMessages = $this->chatbotChat->chatbotMessages()
            ->where($typeCol, ChatbotMessageTypesEnum::BotAction->name)
            ->orderBy(TimestampsEnum::CreatedAt->dbName(), 'desc')
            ->orderBy($idCol, 'desc')
            ->get();

        // Find the closest StartTicket message
        $startTicketMessage = null;
        foreach ($botActionMessages as $botActionMessage) {

            $messageContent = $botActionMessage[$contentCol];
            $messageType = $messageContent[ChatbotActionTypesEnum::KEY_TYPE];

            if ($messageType == ChatbotActionTypesEnum::StartTicket->name) {

                $startTicketMessage = $botActionMessage;
                break;
            }
        }

        // The "StartTicket" message not found
        if (is_null($startTicketMessage))
            return $this->createErrorForMakeTicket($chatbotStep, "Chatbot action[Make Ticket]: The 'StartTicket' message not found");

        $startTicketStep = $startTicketMessage->chatbotStep;
        ########## Start collecting client inputs ##########

        // Get all UserInput Messages
        $userInputMessages = $this->chatbotChat->chatbotMessages()
            ->where($typeCol, ChatbotMessageTypesEnum::Input->name)
            ->where($idCol, '>', $startTicketMessage->$idCol)
            ->orderBy(TimestampsEnum::CreatedAt->dbName(), 'asc')
            ->orderBy($idCol, 'asc')
            ->get();


        if ($userInputMessages->count() < 1) {
            // There is no 'UserInput' between 'StartTicket' message and 'MakeTicket' Message

            return $this->createErrorForMakeTicket($chatbotStep, "Chatbot action[Make Ticket]: There is no 'UserInput' between 'StartTicket' message and 'MakeTicket' Message!", [
                'Step ID of start ticket message' => $startTicketMessage->$chatbotStepIdCol,
                'Start ticket message ID' => $startTicketMessage->$idCol,
            ]);
        }

        try {
            // Creating ticket
            $startTicketMessageContentData = $startTicketMessage[$contentCol][ChatbotActionTypesEnum::KEY_DATA];
            $chatbotStepAction = $chatbotStep[ChatbotStepsTableEnum::Action->dbName()];
            $chatbotStepActionData = $chatbotStepAction[ChatbotActionTypesEnum::KEY_DATA];

            $ticket = new Ticket([
                TicketsTableEnum::OwnerId->dbName() => User::authUser()->$idCol,
                TicketsTableEnum::TicketableType->dbName() => TicketableTypesEnum::ChatbotStep->name,
                TicketsTableEnum::TicketableId->dbName() => $startTicketStep->$idCol,
                TicketsTableEnum::Subject->dbName() => $startTicketMessageContentData[ChatbotActionTypesEnum::KEY_TICKET_SUBJECT],
                TicketsTableEnum::Priority->dbName() => $chatbotStepActionData[ChatbotActionTypesEnum::KEY_TICKET_PRIORITY],
                TicketsTableEnum::Status->dbName() => TicketsStatusEnum::New->name,
            ]);

            if ($ticket->save()) {
                // Creating ticket messages

                try {
                    foreach ($userInputMessages as $userInputMessage) {

                        $makeTicketMessage =  ChatbotUserInputActionEnum::makeTicketMessage($userInputMessage, $ticket);

                        if (!$makeTicketMessage) {

                            // Soft delete to check later with debug logs
                            $ticket[TicketsTableEnum::PrivateNote->dbName()] = "This ticket is not created properly and has been deleted by the system to investigate technical issues, please do not make any changes to the ticket.";
                            $ticket->save();
                            $ticket->delete();

                            return $this->createErrorForMakeTicket($chatbotStep, "Chatbot action[Make Ticket]: Failed to create ticket messages.", [
                                'Step ID of start ticket message' => $startTicketMessage->$chatbotStepIdCol,
                                'Start ticket message ID' => $startTicketMessage->$idCol,
                                'User input message' => json_encode($userInputMessage),
                            ]);
                        }
                    }

                    // Ticket has been created successfully
                    $chatbotMessage = ChatbotActionActionEnum::makeChatMessage($chatbotStep, $this->chatbotChat);
                    if (!is_null($chatbotMessage)) {
                        return JsonResponseHelper::successResponse($this->makeResponseData($chatbotMessage->toArray()), 'Success');
                    } else {
                        return $this->createErrorForMakeTicket($chatbotStep, "Chatbot action[Make Ticket]: Exception error while creating chatbot final message for make ticket action.");
                    }
                } catch (\Throwable $th) {

                    return $this->createErrorForMakeTicket($chatbotStep, "Chatbot action[Make Ticket]: Exception error while creating ticket messages.", [
                        'Step ID of start ticket message' => $startTicketMessage->$chatbotStepIdCol,
                        'Start ticket message ID' => $startTicketMessage->$idCol,
                        'Exception error' => $th->getMessage(),
                    ]);
                }
            }
        } catch (\Throwable $th) {

            return $this->createErrorForMakeTicket($chatbotStep, "Chatbot action[Make Ticket]: Exception error while creating ticket.", [
                'Step ID of start ticket message' => $startTicketMessage->$chatbotStepIdCol,
                'Start ticket message ID' => $startTicketMessage->$idCol,
                'Exception error' => $th->getMessage(),
            ]);
        }

        return JsonResponseHelper::errorResponse('error.UnknownError', __('error.UnknownError'));
    }

    /**
     * Create error for "botActionMakeTicket" function
     *
     * @param  \App\Models\BackOffice\Chatbot\ChatbotStep $chatbotStep
     * @param  string $errorMessage
     * @param  ?array $extraData
     * @return \Illuminate\Http\JsonResponse
     */
    private function createErrorForMakeTicket(ChatbotStep $chatbotStep, string $errorMessage, ?array $extraData = null): JsonResponse
    {
        $errorMessage = sprintf(
            "%s\nChatbot Chat ID: %s\nChatbot 'MakeTicket' Step ID: %s\nUser ID: %s ",
            $errorMessage,
            $this->chatbotChat->id,
            $chatbotStep->id,
            User::authUser()->id

        );

        if (!empty($extraData)) {
            foreach ($extraData as $key => $value) {
                $errorMessage .= sprintf("\n%s: %s", $key, $value);
            }
        }
        LogCreator::createLogError(get_class(), __FUNCTION__, $errorMessage, "Chatbot: Faild to make ticket.");

        return JsonResponseHelper::errorResponse('thisApp.Errors.ChatbotMessenger.FailedToMakeTicket', __('thisApp.Errors.ChatbotMessenger.FailedToMakeTicket'), HttpResponseStatusCode::ExpectationFailed->value);
    }
}
