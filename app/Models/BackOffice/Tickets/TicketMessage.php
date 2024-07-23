<?php

namespace App\Models\BackOffice\Tickets;

use App\Enums\Database\Tables\TicketMessagesTableEnum as TableEnum;
use App\Enums\Database\Tables\TicketsTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Resources\ImageConfigEnum;
use App\Enums\Tickets\TicketMessageTypesEnum;
use App\HHH_Library\general\php\FileAssistant;
use App\Models\SuperClasses\SuperModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TicketMessage extends SuperModel
{
    use HasFactory;

    /**************** Parnet Items ********************/

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->keyType = 'string';
        $this->incrementing = false;

        $this->fillable = [
            TableEnum::UserId->dbName(),
            TableEnum::TicketId->dbName(),
            TableEnum::Type->dbName(),
            TableEnum::Content->dbName(),
        ];

        parent::__construct($attributes);
    }

    /**
     * @override parent boot
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function (self $model) {

            $model[TableEnum::Id->dbName()] = Str::orderedUuid();
            return $model;
        });

        self::saved(function (self $model) {

            /** @var User $user */
            $user = $model->user;
            $ticket = $model->ticket;
            $responderIdCol = TicketsTableEnum::ResponderId->dbName();

            if ($user->isPersonnel() && $user->id != $ticket->$responderIdCol) {
                /** Responder to ticket has been changed */

                $ticket[$responderIdCol] = $user->id;
                $ticket->save();
            }

            $ticket->touch();
        });
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get the user that owns the TicketMessage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, TableEnum::UserId->dbName(), UsersTableEnum::Id->dbName());
    }

    /**
     * Get the ticket that owns the TicketMessage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, TableEnum::TicketId->dbName(), TicketsTableEnum::Id->dbName());
    }
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/

    /**
     * Get profile photo file config
     *
     * @return \App\Enums\Resources\ImageConfigEnum
     */
    public static function getPhotoFileConfig(): ImageConfigEnum
    {
        return ImageConfigEnum::TicketMessage;
    }

    /**
     * Get photo file assistant
     *
     * @param bool $useFallbackPhoto : true => if file not exists, it will be return fallback image (no profile)
     * @return \App\HHH_Library\general\php\FileAssistant
     */
    public function getPhotoFileAssistant(bool $useFallbackPhoto = true): FileAssistant
    {
        $fileConfig = $this->getPhotoFileConfig();

        $fileAssistant = new FileAssistant($fileConfig, $this[TableEnum::Content->dbName()]);

        if ($useFallbackPhoto && !$fileAssistant->isFileExists()) {

            $fileAssistant->setPath($fileConfig->defaultPath());
            $fileAssistant->setName($fileConfig->defaultImage());
        }

        return $fileAssistant;
    }

    /**
     * Get content for dispaly in Html view
     *
     * @return string
     */
    public function getContentForHtml(): string
    {

        $type = $this[TableEnum::Type->dbName()];
        $content = $this[TableEnum::Content->dbName()];

        if ($type === TicketMessageTypesEnum::Text->name) {
            return str_replace("\n", '<br/>', $content);
        } else if ($type === TicketMessageTypesEnum::TicketImage->name) {
            return $this->PhotoUrl;
        } else if ($type === TicketMessageTypesEnum::ChatbotImage->name) {
            return $this->PhotoUrl;
        }

        return "";
    }

    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/

    /**
     * All times are stored in the database based on UTC time(00:00),
     * so time is converted to user local timezone,
     * and selected calendar type
     * based on the user and admin panel settings.
     *
     * @param  mixed $value
     * @return ?string
     */
    public function getCreatedAtAttribute(mixed $value): ?string
    {
        $user = User::authUser();

        return is_null($user) ? $value : $user->convertUTCToLocalTime($value);
    }

    /**
     * All times are stored in the database based on UTC time(00:00),
     * so time is converted to user local timezone,
     * and selected calendar type
     * based on the user and admin panel settings.
     *
     * @param  mixed $value
     * @return ?string
     */
    public function getUpdatedAtAttribute(mixed $value): ?string
    {
        $user = User::authUser();

        return is_null($user) ? $value : $user->convertUTCToLocalTime($value);
    }

    /**
     * This function returns Photo full name.
     * Returns the default Photo file(noPhoto) if it does not exist
     *
     * sample output:
     * "BackOffice/assets_hhh/images/office_profile_photos/no_profile.png"
     *
     * @return ?string
     */
    public function getPhotoFullNameAttribute(): ?string
    {
        return $this->getPhotoFileAssistant()->getFullName();
    }

    /**
     * This function returns Photo url.
     * Returns the default Photo file(noPhoto) if it does not exist
     *
     * sample output:
     * "http://community.cod/assets/upload/images/profile_photos/no_profile.png"
     *
     * @return string
     */
    public function getPhotoUrlAttribute(): string
    {
        return $this->getPhotoFileAssistant()->getUrl();
    }
    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/

    /**
     * Get scope of text messages
     *
     * @param  mixed $query
     * @return Builder
     */
    public function scopeTexts(Builder $query): Builder
    {
        return $query->where(TableEnum::Type->dbName(), TicketMessageTypesEnum::Text->name);
    }

    /**
     * Get scope of image messages
     *
     * @param  mixed $query
     * @return Builder
     */
    public function scopeImages(Builder $query): Builder
    {
        return $query
            ->where(TableEnum::Type->dbName(), TicketMessageTypesEnum::TicketImage->name)
            ->orWhere(TableEnum::Type->dbName(), TicketMessageTypesEnum::ChatbotImage->name);
    }

    /**
     * Get scope of files messages
     *
     * @param  mixed $query
     * @return Builder
     */
    public function scopeFiles(Builder $query): Builder
    {
        return $query->where(TableEnum::Type->dbName(), TicketMessageTypesEnum::File->name);
    }
    /**************** scopes Collection END ********************/

    /**************** scopes ********************/
    /**************** scopes END ********************/
}
