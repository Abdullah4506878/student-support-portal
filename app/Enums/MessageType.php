<?php

namespace App\Enums;

enum MessageType: string
{
    case Message = 'message';
    case InfoRequest = 'info_request';
    case DocumentRequest = 'document_request';
    case StudentResponse = 'student_response';
}
