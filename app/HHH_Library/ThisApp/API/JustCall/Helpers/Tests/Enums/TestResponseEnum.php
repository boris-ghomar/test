<?php

namespace App\HHH_Library\ThisApp\API\JustCall\Helpers\Tests\Enums;

enum TestResponseEnum: string
{
        // Success responses
    case SendText_Success = '{"status":"success","message":"Text sent","id":11024,"data": {"id": 11024,"client_number": "917001231234","justcall_number": "1831231234","body": "Hello!","is_mms": "0","mms": null,"contact_name": " ","datetime": "2021-08-06 14:53:38","agent_name": "Agile JustCall", "agent_id": 10400,"delivery_status": "","is_deleted": false}}';

        // Failed responses
    case SendText_Fail_IncorrectNumber = '{"status":"fail","message":"One of the numbers is incorrect. Please check the number.","correlation_id":"7a2e0ec7dfd4cde8615cbcbcf9526b5f"}';
}
