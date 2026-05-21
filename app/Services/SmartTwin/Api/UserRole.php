<?php

namespace App\Services\SmartTwin\Api;

enum UserRole: string
{
    case Resident = 'resident';
    case Advisor = 'advisor';
}
