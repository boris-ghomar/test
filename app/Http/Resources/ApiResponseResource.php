<?php

namespace App\Http\Resources;

use App\HHH_Library\general\php\traits\TranslateDatabaseField;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiResponseResource extends JsonResource
{
    use TranslateDatabaseField;

}
