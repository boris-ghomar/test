<?php

namespace App\Enums\Users;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\VerificationsTableEnum as TableEnum;
use App\Enums\Database\Tables\VerificationsTableEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Models\General\Verification;
use App\Models\User;
use Carbon\Carbon;

enum VerificationTypesEnum
{
    use EnumActions;

    case Email;
    case Mobile;

    /**
     * Make verification record
     *
     * @param  null|\App\Models\User $user
     * @param int $validMinutes The verification expires after these minutes have elapsed
     * @param ?string $oldValue
     * @param ?string $newValue
     * @return \App\Models\General\Verification
     */
    public function makeVerificationRecord(int $validMinutes, ?User $user, ?string $oldValue, ?string $newValue): ?Verification
    {
        if (is_null($user) && empty($oldValue) && empty($newValue))
            return null;

        $verification = new Verification([

            TableEnum::Type->dbName()       => $this->name,
            TableEnum::UserId->dbName()     => is_null($user) ? null : $user->id,
            TableEnum::OldValue->dbName()   => $oldValue,
            TableEnum::NewValue->dbName()   => $newValue,
            TableEnum::ValidUntil->dbName() => Carbon::now()->addMinutes($validMinutes),
        ]);

        $verification->save();

        return $verification;
    }

    /**
     * Get under verification record
     *
     * @param  null|\App\Models\User $user
     * @param  ?string $oldValue
     * @param  ?string $newValue
     * @return \App\Models\General\Verification
     */
    public function getVerification(?User $user, ?string $oldValue = null, ?string $newValue = null): ?Verification
    {
        if (is_null($user) && empty($oldValue) && empty($newValue))
            return null;

        $verification = Verification::where(VerificationsTableEnum::Type->dbName(), $this->name)
            ->where(VerificationsTableEnum::ValidUntil->dbName(), '>', Carbon::now())
            ->orderBy(TimestampsEnum::UpdatedAt->dbName(), 'desc')
            ->orderBy(VerificationsTableEnum::Id->dbName(), 'desc');

        if (!is_null($user))
            $verification->where(VerificationsTableEnum::UserId->dbName(), $user->id);

        if (!is_null($oldValue))
            $verification->where(VerificationsTableEnum::OldValue->dbName(), $oldValue);

        if (!is_null($newValue))
            $verification->where(VerificationsTableEnum::NewValue->dbName(), $newValue);

        return $verification->first();
    }
}
