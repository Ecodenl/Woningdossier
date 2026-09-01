<?php

namespace App\Services\SmartTwin\Api;

// SmartTwin expects the role as an integer, not a string. Their enum: 0 = Resident, 1 = Advisor.
enum UserRole: int
{
    case Resident = 0;
    case Advisor = 1;
}
