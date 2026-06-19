<?php

namespace App\Enums\SmartTwin;

enum EventType: string
{
    case RESIDENT_SCAN_FINISHED = 'smarttwin.quickscan.finalized';
    case COACH_SCAN_FINISHED    = 'smarttwin.advice.finalized';
}
