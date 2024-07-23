<?php

namespace App\Http\Controllers\SuperClasses;

use App\HHH_Library\general\php\traits\AddAttributesPad;
use App\HHH_Library\general\php\traits\TranslateDatabaseField;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperController extends Controller
{
    use AddAttributesPad;
    use TranslateDatabaseField;
}
