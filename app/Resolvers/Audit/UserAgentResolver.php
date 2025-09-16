<?php

namespace App\Resolvers\Audit;

use OwenIt\Auditing\Contracts\Auditable;

class UserAgentResolver implements \OwenIt\Auditing\Contracts\Resolver
{
    /**
     * {@inheritdoc}
     */
    public static function resolve(Auditable $auditable)
    {
        // Default to "N/A" if the User Agent isn't available
        return null;
    }
}
