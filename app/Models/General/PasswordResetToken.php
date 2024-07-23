<?php

namespace App\Models\General;

use App\Enums\Database\Tables\PasswordResetTokenEnum as TableEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    use HasFactory;

    /**************** Parnet Items ********************/

    const UPDATED_AT = null;

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {

        $this->primaryKey = TableEnum::Email->dbName();

        $this->fillable = [
            TableEnum::Email->dbName(),
            TableEnum::Token->dbName(),
        ];

        parent::__construct($attributes);
    }

    /**************** Parnet Items END ********************/
}
