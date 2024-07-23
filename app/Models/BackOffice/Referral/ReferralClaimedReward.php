<?php

namespace App\Models\BackOffice\Referral;

use App\Enums\Database\Tables\ReferralClaimedRewardsTableEnum as TableEnum;
use App\Enums\Database\Tables\ReferralRewardItemsTableEnum;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralClaimedReward extends SuperModel
{
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
            TableEnum::ReferralSessionId->dbName(),
            TableEnum::RewardItemId->dbName(),
        ];

        parent::__construct($attributes);
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/
    /**
     * Get the referralRewardPackage that owns the ReferralClaimedReward
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referralRewardItem(): BelongsTo
    {
        return $this->belongsTo(ReferralRewardItem::class, TableEnum::RewardItemId->dbName(), ReferralRewardItemsTableEnum::Id->dbName());
    }

    /**
     * Get the referralRewardPackage that owns the ReferralClaimedReward
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referralRewardPackage(): BelongsTo
    {
        return $this->referralRewardItem->referralRewardPackage();
    }
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/
    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/
    /**************** scopes Collection END ********************/

    /**************** scopes ********************/
    /**************** scopes END ********************/
}

