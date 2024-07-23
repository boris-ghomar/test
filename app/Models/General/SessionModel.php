<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionModel extends Model
{
    protected $table = "sessions";
    public $timestamps = false;
}
