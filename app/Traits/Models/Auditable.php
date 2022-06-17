<?php

namespace App\Traits\Models;

use App\Helpers\Arr;
use App\Helpers\HoomdossierSession;

trait Auditable
{
    use \OwenIt\Auditing\Auditable;
}