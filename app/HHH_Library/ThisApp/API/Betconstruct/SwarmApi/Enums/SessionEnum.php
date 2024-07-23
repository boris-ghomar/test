<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums;

use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\HHH_Library\general\php\traits\Enums\EnumSession;

enum SessionEnum: string
{
    use EnumActions;
    use EnumSession;


    case GoogleRecaptchSiteKey = "bc_g_site_key";
    case SwarmSessionId = "bc_swarm_session_id";

    case SessionCreatedAt = "session_created_at";
}
