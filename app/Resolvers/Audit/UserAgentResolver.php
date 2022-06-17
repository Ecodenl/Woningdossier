<?php

namespace App\Resolvers\Audit;

class UserAgentResolver implements \OwenIt\Auditing\Contracts\UserAgentResolver
{
    /**
     * {@inheritdoc}
     */
    public static function resolve()
    {
        // Default to "N/A" if the User Agent isn't available
        return null;
    }
}