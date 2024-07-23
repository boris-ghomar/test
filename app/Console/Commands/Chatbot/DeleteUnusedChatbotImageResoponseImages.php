<?php

namespace App\Console\Commands\Chatbot;

use App\Enums\Database\Tables\ChatbotStepsTableEnum;
use App\Enums\Resources\ImageConfigEnum;
use App\HHH_Library\general\php\FileAssistant;
use App\Models\BackOffice\Chatbot\ChatbotStep;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class DeleteUnusedChatbotImageResoponseImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'App-Cronjob:Delete-Unused-Chatbot-ImageResoponse-Images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete unsed chatbot ImageResoponse images that do not exist in the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fileAssistant = new FileAssistant(ImageConfigEnum::ChatbotImageResponse);

        $images = $fileAssistant->files();

        foreach ($images as $image) {

            $name = Str::of($image)->afterLast("/")->toString();

            if (!ChatbotStep::where(ChatbotStepsTableEnum::Action->dbName(), 'like', '%' . $name . '%')->exists()) {
                $fileAssistant->deleteFile($image);
            }
        }
    }
}
