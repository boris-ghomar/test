<?php

namespace App\Console\Commands\Users;

use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Resources\ImageConfigEnum;
use App\HHH_Library\general\php\FileAssistant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class DeleteUnusedProfileImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'App-Cronjob:delete-unsed-profile-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes profile pictures that do not exist in the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fileAssistant = new FileAssistant(ImageConfigEnum::ProfilePhoto);

        $profilePhotos = $fileAssistant->files();

        foreach ($profilePhotos as $photo) {

            $name = Str::of($photo)->afterLast("/")->toString();

            if (!User::where(UsersTableEnum::ProfilePhotoName->dbName(), $name)->exists()) {
                $fileAssistant->deleteFile($photo);
            }
        }
    }
}
