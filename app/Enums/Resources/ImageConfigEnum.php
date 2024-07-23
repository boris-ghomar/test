<?php

namespace App\Enums\Resources;

use App\Enums\Resources\Interfaces\ImageConfigInterface;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum ImageConfigEnum implements ImageConfigInterface
{
    use EnumActions;


    case Default;

    case ProfilePhoto;
    case AdminPanelBigLogo;
    case AdminPanelMiniLogo;
    case AdminPanelFavicon;
    case CommunityBigLogo;
    case CommunityMiniLogo;
    case CommunityFavicon;
    case PostMainPhoto;
    case TicketMessage;
    case ChatbotImageResponse;
    case ChatbotProfileImage;
    case ChatbotUserInputImage;

    /**
     * Get storage disk
     *
     * @return string
     */
    public function disk(): string
    {
        return 'root_public'; // disk name comes from: config/filesystems.php
    }

    /**
     * Get storage path in disk
     *
     * @return string
     */
    public function path(): string
    {
        return match ($this) {

            self::Default               => 'assets/general/images/defaults/',
            self::ProfilePhoto          => 'assets/upload/images/profile_photos/',
            self::AdminPanelBigLogo     => 'assets/upload/images/admin_panel/',
            self::AdminPanelMiniLogo    => 'assets/upload/images/admin_panel/',
            self::AdminPanelFavicon     => 'assets/upload/images/admin_panel/',
            self::CommunityBigLogo      => 'assets/upload/images/community/',
            self::CommunityMiniLogo     => 'assets/upload/images/community/',
            self::CommunityFavicon      => 'assets/upload/images/community/',
            self::PostMainPhoto         => 'assets/upload/images/posts/',
            self::TicketMessage         => 'assets/upload/images/tickets/',
            self::ChatbotImageResponse  => 'assets/upload/images/chatbot/response_images/',
            self::ChatbotProfileImage   => 'assets/upload/images/chatbot/profile_images/',
            self::ChatbotUserInputImage => 'assets/upload/images/chatbot/user_input_images/',

            default => ''
        };
    }

    /**
     * Get default images path.
     * System default image path.
     *
     * @return string
     */
    public function defaultPath(): string
    {
        return match ($this) {

            self::Default               => self::Default->path(),
            self::ProfilePhoto          => self::Default->path() . 'profile_photos/',
            self::AdminPanelBigLogo     => self::Default->path() . 'admin_panel/',
            self::AdminPanelMiniLogo    => self::Default->path() . 'admin_panel/',
            self::AdminPanelFavicon     => self::Default->path() . 'admin_panel/',
            self::CommunityBigLogo      => self::Default->path() . 'community/',
            self::CommunityMiniLogo     => self::Default->path() . 'community/',
            self::CommunityFavicon      => self::Default->path() . 'community/',
            self::PostMainPhoto         => self::Default->path() . 'post/',
            self::TicketMessage         => self::Default->path() . 'post/',
            self::ChatbotImageResponse  => self::Default->path() . 'chatbot/',
            self::ChatbotProfileImage   => self::Default->path() . 'chatbot/',
            self::ChatbotUserInputImage => self::Default->path() . 'chatbot/',

            default => self::Default->path()
        };
    }

    /**
     * Get default images name.
     * When there is no image, the app uses these images.
     *
     * @return string
     */
    public function defaultImage(): string
    {
        return match ($this) {

            self::Default               => 'no_image.png',
            self::ProfilePhoto          => 'no_profile_dark.png',
            self::AdminPanelBigLogo     => 'AdminPanelLogo_big.png',
            self::AdminPanelMiniLogo    => 'AdminPanelLogo_mini.png',
            self::AdminPanelFavicon     => 'favicon.ico',
            self::CommunityBigLogo      => 'CommunityLogo_big.png',
            self::CommunityMiniLogo     => 'CommunityLogo_mini.png',
            self::CommunityFavicon      => 'favicon.ico',
            self::PostMainPhoto         => 'main_photo_no_image.png',
            self::TicketMessage         => 'main_photo_no_image.png',
            self::ChatbotImageResponse  => 'no_image.png',
            self::ChatbotProfileImage   => 'profile.png',
            self::ChatbotUserInputImage => 'no_image.png',

            default => 'no_image.png'
        };
    }

    /**
     * Get full path of default image.
     *
     * @return string
     */
    public function defaultImagePath(): string
    {

        return $this->defaultPath() . $this->defaultImage();
    }

    /**
     * Get resize width of image in pixel.
     *
     * When it is necessary to change the dimensions of the image,
     * the app uses these dimensions.
     *
     * @return int
     */
    public function resizeWidth(): int
    {
        $default = 100;

        return match ($this) {

            self::Default               => $default,
            self::ProfilePhoto          => $default,
            self::AdminPanelBigLogo     => $default,
            self::AdminPanelMiniLogo    => $default,
            self::AdminPanelFavicon     => $default,
            self::CommunityBigLogo      => $default,
            self::CommunityMiniLogo     => $default,
            self::CommunityFavicon      => $default,
            self::PostMainPhoto         => 300,
            self::TicketMessage         => 800,
            self::ChatbotImageResponse  => 800,
            self::ChatbotProfileImage   => 300,
            self::ChatbotUserInputImage => 800,

            default => $default
        };
    }

    /**
     * Get resize height of image in pixel.
     *
     * When it is necessary to change the dimensions of the image,
     * the app uses these dimensions.
     *
     * @return int
     */
    public function resizeHeight(): int
    {
        $default = 100;

        return match ($this) {

            self::Default               => $default,
            self::ProfilePhoto          => $default,
            self::AdminPanelBigLogo     => $default,
            self::AdminPanelMiniLogo    => $default,
            self::AdminPanelFavicon     => $default,
            self::CommunityBigLogo      => $default,
            self::CommunityMiniLogo     => $default,
            self::CommunityFavicon      => $default,
            self::PostMainPhoto         => 300,
            self::TicketMessage         => 800,
            self::ChatbotImageResponse  => 800,
            self::ChatbotProfileImage   => 300,
            self::ChatbotUserInputImage => 800,

            default => $default
        };
    }

    /**
     * Get resize dpi of image.
     *
     * When it is necessary to change the dimensions of the image,
     * the app uses these dimensions.
     *
     * @return int
     */
    public function resizeDpi(): int
    {
        $default = 96;

        return match ($this) {

            self::Default               => $default,
            self::ProfilePhoto          => $default,
            self::AdminPanelBigLogo     => $default,
            self::AdminPanelMiniLogo    => $default,
            self::AdminPanelFavicon     => $default,
            self::CommunityBigLogo      => $default,
            self::CommunityMiniLogo     => $default,
            self::CommunityFavicon      => $default,
            self::PostMainPhoto         => $default,
            self::TicketMessage         => $default,
            self::ChatbotImageResponse  => $default,
            self::ChatbotProfileImage   => $default,
            self::ChatbotUserInputImage => $default,

            default => $default
        };
    }

    /**
     * Get convert type of image.
     *
     * When it needs to change the image type,
     * the app will convert the image to this type of image.
     *
     * @return string
     */
    public function convertType(): string
    {
        $default = 'jpg';

        return match ($this) {

            self::Default               => $default,
            self::ProfilePhoto          => $default,
            self::AdminPanelBigLogo     => $default,
            self::AdminPanelMiniLogo    => $default,
            self::AdminPanelFavicon     => $default,
            self::CommunityBigLogo      => $default,
            self::CommunityMiniLogo     => $default,
            self::CommunityFavicon      => $default,
            self::PostMainPhoto         => $default,
            self::TicketMessage         => $default,
            self::ChatbotImageResponse  => $default,
            self::ChatbotProfileImage   => $default,
            self::ChatbotUserInputImage => $default,

            default => $default
        };
    }

    /**
     * Get acceptable mimes of image.
     *
     * @param  mixed $getAsArray (optinal) return $getAsArray ? array : string (sample: 'jpeg,jpg,png');
     * @return string|array
     */
    public function mimes(bool $getAsArray = false): string|array
    {
        $defaultForImage = ['jpeg', 'jpg', 'png'];
        $defaultForFavicon = ['ico'];

        $res =  match ($this) {

            self::Default               => $defaultForImage,
            self::ProfilePhoto          => $defaultForImage,
            self::AdminPanelBigLogo     => $defaultForImage,
            self::AdminPanelMiniLogo    => $defaultForImage,
            self::AdminPanelFavicon     => $defaultForFavicon,
            self::CommunityBigLogo      => $defaultForImage,
            self::CommunityMiniLogo     => $defaultForImage,
            self::CommunityFavicon      => $defaultForFavicon,
            self::PostMainPhoto         => $defaultForImage,
            self::TicketMessage         => $defaultForImage,
            self::ChatbotImageResponse  => $defaultForImage,
            self::ChatbotProfileImage   => $defaultForImage,
            self::ChatbotUserInputImage => $defaultForImage,

            default => $defaultForImage
        };

        return $getAsArray ? $res : implode(",", $res);
    }

    /**
     * Get acceptable mimes of image for use in upload input field.
     *
     * @param  mixed $getAsArray (optinal) return $getAsArray ? array : string (sample: '.jpeg,.jpg,.png');
     * @return string|array
     */
    public function acceptableMimesForUpload(bool $getAsArray = false): string|array
    {
        $res = [];
        $mimes = $this->mimes(true);

        foreach ($mimes as $mime) {
            array_push($res, '.' . $mime);
        }

        return $getAsArray ? $res : implode(",", $res);
    }

    /**
     * Minimum acceptable width of image in pixel.
     *
     *
     * @return int
     */
    public function minWidth(): int
    {
        $defaultForImage = 100;
        $defaultForFavicon = 16;

        return match ($this) {

            self::Default               => $defaultForImage,
            self::ProfilePhoto          => $defaultForImage,
            self::AdminPanelBigLogo     => $defaultForImage,
            self::AdminPanelMiniLogo    => $defaultForImage,
            self::AdminPanelFavicon     => $defaultForFavicon,
            self::CommunityBigLogo      => $defaultForImage,
            self::CommunityMiniLogo     => $defaultForImage,
            self::CommunityFavicon      => $defaultForFavicon,
            self::PostMainPhoto         => 300,
            self::TicketMessage         => 10,
            self::ChatbotImageResponse  => 10,
            self::ChatbotProfileImage   => 10,
            self::ChatbotUserInputImage => 10,

            default => $defaultForImage
        };
    }

    /**
     * Minimum acceptable height of image in pixel.
     *
     *
     * @return int
     */
    public function minHeight(): int
    {
        $defaultForImage = 100;
        $defaultForFavicon = 16;

        return match ($this) {

            self::Default               => $defaultForImage,
            self::ProfilePhoto          => $defaultForImage,
            self::AdminPanelBigLogo     => $defaultForImage,
            self::AdminPanelMiniLogo    => $defaultForImage,
            self::AdminPanelFavicon     => $defaultForFavicon,
            self::CommunityBigLogo      => $defaultForImage,
            self::CommunityMiniLogo     => $defaultForImage,
            self::CommunityFavicon      => $defaultForFavicon,
            self::PostMainPhoto         => 300,
            self::TicketMessage         => 10,
            self::ChatbotImageResponse  => 10,
            self::ChatbotProfileImage   => 10,
            self::ChatbotUserInputImage => 10,

            default => $defaultForImage
        };
    }

    /**
     * Minimum acceptable size of image in kb.
     *
     *
     * @return int
     */
    public function minSize(): int
    {
        $defaultForImage = 5;
        $defaultForFavicon = 5;

        return match ($this) {

            self::Default               => $defaultForImage,
            self::ProfilePhoto          => $defaultForImage,
            self::AdminPanelBigLogo     => $defaultForImage,
            self::AdminPanelMiniLogo    => $defaultForImage,
            self::AdminPanelFavicon     => $defaultForFavicon,
            self::CommunityBigLogo      => $defaultForImage,
            self::CommunityMiniLogo     => $defaultForImage,
            self::CommunityFavicon      => $defaultForFavicon,
            self::PostMainPhoto         => $defaultForImage,
            self::TicketMessage         => $defaultForImage,
            self::ChatbotImageResponse  => $defaultForImage,
            self::ChatbotProfileImage   => $defaultForImage,
            self::ChatbotUserInputImage => $defaultForImage,

            default => $defaultForImage
        };
    }

    /**
     * Maximum acceptable width of image in pixel.
     *
     *
     * @return int
     */
    public function maxWidth(): int
    {
        $defaultForImage = 500;
        $defaultForFavicon = 500;

        return match ($this) {

            self::Default               => $defaultForImage,
            self::ProfilePhoto          => 5000,
            self::AdminPanelBigLogo     => $defaultForImage,
            self::AdminPanelMiniLogo    => $defaultForImage,
            self::AdminPanelFavicon     => $defaultForFavicon,
            self::CommunityBigLogo      => $defaultForImage,
            self::CommunityMiniLogo     => $defaultForImage,
            self::CommunityFavicon      => $defaultForFavicon,
            self::PostMainPhoto         => 700,
            self::TicketMessage         => 5000,
            self::ChatbotImageResponse  => 5000,
            self::ChatbotProfileImage   => 700,
            self::ChatbotUserInputImage => 5000,

            default => $defaultForImage
        };
    }

    /**
     * Maximum acceptable height of image in pixel.
     *
     *
     * @return int
     */
    public function maxHeight(): int
    {
        $defaultForImage = 500;
        $defaultForFavicon = 500;

        return match ($this) {

            self::Default               => $defaultForImage,
            self::ProfilePhoto          => 5000,
            self::AdminPanelBigLogo     => $defaultForImage,
            self::AdminPanelMiniLogo    => $defaultForImage,
            self::AdminPanelFavicon     => $defaultForFavicon,
            self::CommunityBigLogo      => $defaultForImage,
            self::CommunityMiniLogo     => $defaultForImage,
            self::CommunityFavicon      => $defaultForFavicon,
            self::PostMainPhoto         => 700,
            self::TicketMessage         => 5000,
            self::ChatbotImageResponse  => 5000,
            self::ChatbotProfileImage   => 700,
            self::ChatbotUserInputImage => 5000,

            default => $defaultForImage
        };
    }

    /**
     * Maximum acceptable size of image in kb.
     *
     *
     * @return int
     */
    public function maxSize(): int
    {
        $defaultForImage = 500;
        $defaultForFavicon = 500;

        return match ($this) {

            self::Default               => $defaultForImage,
            self::ProfilePhoto          => 5000,
            self::AdminPanelBigLogo     => $defaultForImage,
            self::AdminPanelMiniLogo    => $defaultForImage,
            self::AdminPanelFavicon     => $defaultForFavicon,
            self::CommunityBigLogo      => $defaultForImage,
            self::CommunityMiniLogo     => $defaultForImage,
            self::CommunityFavicon      => $defaultForFavicon,
            self::PostMainPhoto         => 1024,
            self::TicketMessage         => 10240,
            self::ChatbotImageResponse  => 10240,
            self::ChatbotProfileImage   => 1024,
            self::ChatbotUserInputImage => 10240,

            default => $defaultForImage
        };
    }
}
