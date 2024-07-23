<?php

namespace App\Notifications\Site\Referral;

use App\Enums\Database\Tables\ReferralRewardItemsTableEnum;
use App\Enums\Database\Tables\ReferralRewardPaymentsTableEnum;
use App\Enums\General\CurrencyEnum;
use App\Enums\Referral\ReferralRewardTypeEnum;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\Models\BackOffice\Referral\ReferralRewardPayment;
use App\Models\General\Notification;
use App\Models\User;
use App\Notifications\SuperClasses\SuperDatabaseNotification;
use Illuminate\Bus\Queueable;

class ReferralRewardPaymentDoneNotification extends SuperDatabaseNotification
{
    use Queueable;

    private $referralRewardPaymentId;

    /**
     * Create a new notification instance.
     *
     *
     * @param int $replyId
     * @return void
     */
    public function __construct(int $referralRewardPaymentId)
    {
        $this->referralRewardPaymentId = $referralRewardPaymentId;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  object  $notifiable
     * @return array
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  object  $notifiable
     * @return array
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'referralRewardPaymentId'  => $this->referralRewardPaymentId,
        ];
    }

    /******************************* HHH ********************************/

    /******************** Implements ********************/

    /**
     * This function returns the background
     * "CSS Class" color  of the icon for display.
     *
     * @return string ::Sample: "bg-warning" | "bg-danger" | "bg-info"
     */
    public static function getIconBgClass(): string
    {
        return config('hhh_config.notification.categories.success.iconBgClass');
    }

    /**
     * This function returns the icon "CSS Class" name.
     *
     * @return string ::Sample: "fa fa-id-card" or config('hhh_config.fontIcons.employment')
     */
    public static function getIconViewClass(): string
    {
        return config('hhh_config.fontIcons.general.payment');
    }

    /**
     * This function returns the subject of notification.
     * May be you need to return translated subject.
     *
     * @return string ::Sample: "User workgroup changed!"
     */
    public static function getSubject(): string
    {
        return trans('notifications.ReferralRewardPaymentDone.subject');
    }

    /**
     * This function returns the message of notification.
     * May be you need to return translated subject.
     *
     * @param string $notificationId
     * @return ?string ::Sample: "!!User workgroup has been changed!!"
     */
    public static function getMessage(string $notificationId): ?string
    {
        try {
            if ($notification = Notification::find($notificationId)) {


                $notificationData = $notification->data;
                $referralRewardPaymentId = $notificationData['referralRewardPaymentId'];

                if ($referralRewardPayment = ReferralRewardPayment::find($referralRewardPaymentId)) {

                    /** @var User $user */
                    $user = $referralRewardPayment->user;
                    $userExtra = $user->userExtra;

                    $referralRewardItem = $referralRewardPayment->referralRewardItem()->withTrashed()->first();

                    /** @var CurrencyEnum  $userCurrency*/
                    $userCurrency = CurrencyEnum::getCase(strtoupper($userExtra[ClientModelEnum::CurrencyId->dbName()]));

                    $amount = number_format($referralRewardPayment[ReferralRewardPaymentsTableEnum::Amount->dbName()], 2);
                    $amount = sprintf("%s %s", $amount, $userCurrency->name);

                    $rewardName = $referralRewardItem[ReferralRewardItemsTableEnum::DisplayName->dbName()];

                    /** @var  ReferralRewardTypeEnum $rewardType*/
                    $rewardType = ReferralRewardTypeEnum::getCase($referralRewardItem[ReferralRewardItemsTableEnum::Type->dbName()]);

                    $message = trans(
                        'notifications.ReferralRewardPaymentDone.message',
                        [
                            'amount'        => $amount,
                            'rewardName'    => $rewardName,
                            'rewardType'    => $rewardType->translate(),
                        ]
                    );

                    return $message;
                } else {

                    $notification->delete();
                    return __('thisApp.Errors.Tickets.TicketDeleted');
                }
            }
        } catch (\Throwable $th) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                'Notification dispatching error'
            );
        }

        return null;
    }
    /******************** Implements ********************/


    /******************************* HHH END ********************************/
}
