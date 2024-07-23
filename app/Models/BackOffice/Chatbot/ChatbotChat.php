<?php

namespace App\Models\BackOffice\Chatbot;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\ChatbotChatsTableEnum as TableEnum;
use App\Enums\Database\Tables\ChatbotMessagesTableEnum;
use App\Enums\Database\Tables\ChatbotsTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Models\SuperClasses\SuperModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatbotChat extends SuperModel
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
        $this->fillable = [
            TableEnum::UserId->dbName(),
            TableEnum::ChatbotId->dbName(),
            TableEnum::Status->dbName(),
        ];
        parent::__construct($attributes);
    }

    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get the user that owns the ChatbotChat
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, TableEnum::UserId->dbName(), UsersTableEnum::Id->dbName());
    }

    /**
     * Get the chatbot that owns the ChatbotChat
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chatbot(): BelongsTo
    {
        return $this->belongsTo(Chatbot::class, TableEnum::ChatbotId->dbName(), ChatbotsTableEnum::Id->dbName());
    }

    /**
     * Get all of the chatbotMessages for the ChatbotChat
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chatbotMessages(): HasMany
    {
        return $this->hasMany(ChatbotMessage::class, ChatbotMessagesTableEnum::ChatbotChatId->dbName(), TableEnum::Id->dbName());
    }
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/

    /**
     * Get last message of ChatbotChat
     *
     * @return \App\Models\BackOffice\Chatbot\ChatbotMessage
     */
    public function getLastMessage(): ?ChatbotMessage
    {
        return $this->chatbotMessages()
            ->orderBy(TimestampsEnum::CreatedAt->dbName(), 'desc')
            ->orderBy(TableEnum::Id->dbName(), 'desc')
            ->first();
    }
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/
    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/
    /**************** scopes Collection END ********************/

    /**************** scopes ********************/
    /**************** scopes END ********************/
}
