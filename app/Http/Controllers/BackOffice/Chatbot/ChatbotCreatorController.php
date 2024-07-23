<?php

namespace App\Http\Controllers\BackOffice\Chatbot;


use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Chatbot\ChatbotStepActions\ChatbotActionActionEnum;
use App\Enums\Chatbot\ChatbotStepActions\ChatbotResponseActionEnum;
use App\Enums\Chatbot\ChatbotStepActions\ChatbotResponseTypesEnum;
use App\Enums\Chatbot\ChatbotStepTypesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\ChatbotStepsTableEnum as TableEnum;
use App\Enums\Resources\ImageConfigEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\FileAssistant;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\Http\Controllers\SuperClasses\SuperController;
use App\Models\BackOffice\Chatbot\Chatbot;
use App\Models\BackOffice\Chatbot\ChatbotStep;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotCreatorController extends SuperController
{
    /**
     * Get chatbot steps tree
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStepsTree(Request $request): JsonResponse
    {
        $chatbotId = $request->input('chatbot_id');

        if ($chatbot = Chatbot::find($chatbotId)) {

            $data = [
                'chatbotStepsTree' => $chatbot->getStepsTree(),
            ];
            return JsonResponseHelper::successResponse($data, 'success');
        } else {
            return JsonResponseHelper::errorResponse('thisApp.Errors.Chatbot.ChatbotNotFound', __('thisApp.Errors.Chatbot.ChatbotNotFound'));
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * * @return \Illuminate\Http\JsonResponse
     */
    public function addNewStep(Request $request): JsonResponse
    {
        $this->authorize(PermissionAbilityEnum::update->name, Chatbot::class);

        try {
            $chatbotStep = new ChatbotStep();
            $chatbotStep->fill($request->all());

            $chatbotStep[TableEnum::Position->dbName()] = $this->getNewStepPosition($chatbotStep);

            $chatbotStep->save();

            $this->modifyStepPosition($chatbotStep->chatbot->id);

            $data = [
                'chatbotStepsTree' => $chatbotStep->chatbot->getStepsTree(),
            ];
            return JsonResponseHelper::successResponse($data, 'success');
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage());
        }

        return JsonResponseHelper::errorResponse('error.UnknownError', __('error.UnknownError'));
    }


    /**
     * Get new step position
     *
     * @param  \App\Models\BackOffice\Chatbot\ChatbotStep $chatbotStep
     * @return int
     */
    private function getNewStepPosition(ChatbotStep $chatbotStep): int
    {
        $chatbotIdCol = TableEnum::ChatbotId->dbName();
        $positionCol = TableEnum::Position->dbName();
        $updatedAtCol = TimestampsEnum::UpdatedAt->dbName();

        $lastStep = ChatbotStep::where($chatbotIdCol, $chatbotStep->$chatbotIdCol)
            ->orderBy($positionCol, 'desc')
            ->orderBy($updatedAtCol, 'desc')
            ->first();

        return is_null($lastStep) ? 1 : $lastStep->$positionCol + 1;
    }

    /**
     * Modify steps position
     *
     * @param int $chatbotId
     * @return void
     */
    private function modifyStepPosition(int $chatbotId): void
    {
        $chatbotIdCol = TableEnum::ChatbotId->dbName();
        $parentIdCol = TableEnum::ParentId->dbName();
        $positionCol = TableEnum::Position->dbName();
        $updatedAtCol = TimestampsEnum::UpdatedAt->dbName();


        $steps = ChatbotStep::where($chatbotIdCol, $chatbotId)
            ->orderBy($parentIdCol, 'asc')
            ->orderBy($positionCol, 'asc')
            ->orderBy($updatedAtCol, 'desc')
            ->get();

        $positionCounter = 1;
        foreach ($steps as $step) {

            if ($step->$positionCol !== $positionCounter) {

                $step->$positionCol = $positionCounter;
                $step->save();
            }
            $positionCounter++;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteStep(Request $request): JsonResponse
    {
        $this->authorize(PermissionAbilityEnum::update->name, Chatbot::class);

        $chatbotId = $request->input('chatbot_id');
        $stepId = $request->input('stepId');
        $deleteChilds = ($request->input('deleteChilds'));
        $deleteChilds = CastEnum::Boolean->cast($request->input('deleteChilds'));

        try {

            if ($deleteChilds) {
                // Delete step with step childs

                $stepIds = $this->getStepsIds($chatbotId, $stepId);

                if (!empty($stepIds)) {

                    ChatbotStep::whereIn(TableEnum::Id->dbName(), $stepIds)->delete();
                }
            } else {
                // Move step childs to upper parent and delete step
                $parentIdCol = TableEnum::ParentId->dbName();

                if ($step = ChatbotStep::find($stepId)) {

                    $stepChilds = $step->childs;

                    foreach ($stepChilds as $stepChild) {

                        $stepChild->$parentIdCol = $step->$parentIdCol;
                        $stepChild->save();
                    }

                    $step->delete();
                }
            }

            $this->modifyStepPosition($chatbotId);

            $chatbot = Chatbot::find($chatbotId);

            $data = [
                'chatbotStepsTree' => $chatbot->getStepsTree(),
            ];
            return JsonResponseHelper::successResponse($data, 'success');
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage());
        }

        return JsonResponseHelper::errorResponse('error.UnknownError', __('error.UnknownError'));
    }


    /**
     * Get chatbot steps tree
     *
     * @param int $chatbotId
     * @param  int $stepId
     * @return array
     */
    private function getStepsIds(int $chatbotId, int $stepId): array
    {
        $idCol = TableEnum::Id->dbName();
        $parentIdCol = TableEnum::ParentId->dbName();

        $stepIds = [$stepId];


        $stepChildsIds = ChatbotStep::where(TableEnum::ChatbotId->dbName(), $chatbotId)
            ->select($idCol, $parentIdCol)
            ->where(TableEnum::ParentId->dbName(), $stepId)
            ->pluck($idCol)
            ->toArray();

        foreach ($stepChildsIds as $childStepId) {

            $stepIds = array_merge($stepIds, $this->getStepsIds($chatbotId,  $childStepId));
        }

        return $stepIds;
    }

    /**
     * Move Step to new position
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function moveStep(Request $request): JsonResponse
    {

        $idCol = TableEnum::Id->dbName();
        $chatbotIdCol = TableEnum::ChatbotId->dbName();
        $parentIdCol = TableEnum::ParentId->dbName();
        $positionCol = TableEnum::Position->dbName();
        $typeCol = TableEnum::Type->dbName();
        $actionCol = TableEnum::Action->dbName();

        $chatbotId = $request->input('chatbot_id');
        $stepId = $request->input('stepId');
        $moveType = $request->input('moveType'); // above|under|after|before
        $tragetStepId = $request->input('moveTragetStepId');

        $allowedMoveTypes = ['under', 'after', 'before'];

        // Validation of input data
        if (empty($tragetStepId))
            return JsonResponseHelper::errorResponse('thisApp.Errors.Chatbot.chatbotMoveTargetIdRequired', __('thisApp.Errors.Chatbot.chatbotMoveTargetIdRequired'));

        if (!in_array($moveType, $allowedMoveTypes))
            return JsonResponseHelper::errorResponse('thisApp.Errors.Chatbot.chatbotMoveTargetIdRequired', __('thisApp.Errors.Chatbot.chatbotMoveTargetIdRequired'));

        if ($stepId == $tragetStepId)
            return JsonResponseHelper::errorResponse('thisApp.Errors.Chatbot.chatbotMoveSameId', __('thisApp.Errors.Chatbot.chatbotMoveSameId'));

        /** @var ChatbotStep $chatbotStep */
        $chatbotStep = ChatbotStep::where($chatbotIdCol, $chatbotId)
            ->where($idCol, $stepId)
            ->first();

        if (is_null($chatbotStep))
            return JsonResponseHelper::errorResponse('general.NotFoundItem', __('general.NotFoundItem'));

        /** @var ChatbotStep $chatbotStepTarget */
        $chatbotStepTarget = ChatbotStep::where($chatbotIdCol, $chatbotId)
            ->where($idCol, $tragetStepId)
            ->first();

        if (is_null($chatbotStepTarget))
            return JsonResponseHelper::errorResponse('thisApp.Errors.Chatbot.chatbotMoveTargetStepNotFound', __('thisApp.Errors.Chatbot.chatbotMoveTargetStepNotFound', ['targetStepId' => $tragetStepId]));

        if ($chatbotStepTarget[$typeCol] == ChatbotStepTypesEnum::BotAction->name && $moveType == "under") {

            $action = $chatbotStepTarget[$actionCol];
            $isFinalStep = $action[ChatbotActionActionEnum::Data->name]['IsFinalStep'];
            if ($isFinalStep)
                return JsonResponseHelper::errorResponse('thisApp.Errors.Chatbot.chatbotMoveTargetStepIsFinalStep', __('thisApp.Errors.Chatbot.chatbotMoveTargetStepIsFinalStep'));
        }

        // Move step to subset step
        if ($chatbotStepTarget->isSubsetOf($chatbotStep))
            return JsonResponseHelper::errorResponse('thisApp.Errors.Chatbot.chatbotMoveToSubset', __('thisApp.Errors.Chatbot.chatbotMoveToSubset'));

        // Start to move step to new position
        if ($moveType == "under") {

            $chatbotStep[$parentIdCol] = $chatbotStepTarget[$idCol];
            $chatbotStep[$positionCol] = $this->getNewStepPosition($chatbotStep);
            $chatbotStep->save();
        } else if ($moveType == "after") {

            $chatbotStep[$parentIdCol] = $chatbotStepTarget[$parentIdCol];
            $chatbotStep[$positionCol] = $chatbotStepTarget[$positionCol] + 1;
            $chatbotStep->save();
        } else if ($moveType == "before") {

            $chatbotStep[$parentIdCol] = $chatbotStepTarget[$parentIdCol];
            $chatbotStep[$positionCol] = $chatbotStepTarget[$positionCol];
            $chatbotStep->save();
        }

        $this->modifyStepPosition($chatbotId);

        $data = [
            'chatbotStepsTree' => $chatbotStep->chatbot->getStepsTree(),
        ];
        return JsonResponseHelper::successResponse($data, 'success');
    }

    /**
     * Update Step
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStep(Request $request): JsonResponse
    {
        // return JsonResponseHelper::errorResponse(null, $request->all());

        $stepId = $request->input('stepId');

        if ($chatbotStep = ChatbotStep::find($stepId)) {

            try {

                $fillabelItems = [
                    TableEnum::Name->dbName(),
                    TableEnum::Action->dbName(),
                    TableEnum::Position->dbName(),
                ];

                $chatbotStep->fill($request->only($fillabelItems));

                $this->storeResponseImageFile($request, $chatbotStep);

                $chatbotStep->save();

                $data = [
                    'chatbotStepsTree' => $chatbotStep->chatbot->getStepsTree(),
                ];
                return JsonResponseHelper::successResponse($data, 'success');
            } catch (\Throwable $th) {
                return JsonResponseHelper::errorResponse(null, $th->getMessage());
            }
        } else
            return JsonResponseHelper::errorResponse('general.NotFoundItem', __('general.NotFoundItem'));

        return JsonResponseHelper::errorResponse('error.UnknownError', __('error.UnknownError'));
    }

    /**
     * Store ResponseImage File
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\BackOffice\Chatbot\ChatbotStep $chatbotStep
     * @return ?string
     */
    private function storeResponseImageFile(Request $request, ChatbotStep $chatbotStep): ?string
    {

        $fileInputFieldName = "ResponseImageFile";

        // Return null if request does not have file for upload
        if (!$request->hasFile($fileInputFieldName))
            return null;

        // Return null if the step type is not BotResponse
        if ($chatbotStep[TableEnum::Type->dbName()] != ChatbotStepTypesEnum::BotResponse->name)
            return null;

        $stepAction = $chatbotStep[TableEnum::Action->dbName()];
        // Return null if the step action type is not Image
        if ($stepAction[ChatbotResponseActionEnum::Type->name] != ChatbotResponseTypesEnum::Image->name)
            return null;

        // Upload file and replace with old file
        if ($lastStepData = ChatbotStep::find($chatbotStep[TableEnum::Id->dbName()])) {

            try {
                $lastImageName = $lastStepData[TableEnum::Action->dbName()][ChatbotResponseActionEnum::Data->name]['FileName'];
            } catch (\Throwable $th) {
                // If step type changed from another types to ImageResponse and FileName did not set
                $lastImageName = null;
            }

            $lastFile = new FileAssistant(ImageConfigEnum::ChatbotImageResponse, $lastImageName);
            $storedFileName = $lastFile->storeUploadedFile($request, $fileInputFieldName);

            if (!is_null($storedFileName)) {
                $stepAction[ChatbotResponseActionEnum::Data->name]['FileName'] = $storedFileName;
                $chatbotStep[TableEnum::Action->dbName()] = $stepAction;
            }
            return $storedFileName;
        }

        return null;
    }
}
