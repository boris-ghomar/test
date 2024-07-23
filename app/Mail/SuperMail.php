<?php

namespace App\Mail;

use App\Enums\Settings\AppSettingsEnum;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

abstract class SuperMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;


    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        $this->locale(LocaleEnum::getSessionLocale()->value);
    }

    /**
     * Get app name
     *
     * @return string
     */
    protected function getAppName(): string
    {
        return __('thisApp.AppName');
    }

    /**
     * Get relative path of site logo
     *
     * @return string
     */
    protected function getSiteLogoFile(): string
    {
        return AppSettingsEnum::CommunityBigLogo->getImageRelativePath();
    }
}
