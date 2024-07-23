<?php

namespace App\Models\SuperClasses;

use App\HHH_Library\general\php\traits\CheckboxInputFiled;
use App\HHH_Library\general\php\traits\MaskModelAttribute;
use App\HHH_Library\general\php\traits\ModelSuperScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperModel extends Model
{
    use HasFactory;
    use ModelSuperScopes;
    use CheckboxInputFiled;
    use MaskModelAttribute;
}
