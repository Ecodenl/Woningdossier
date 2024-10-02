<?php

namespace App\Resolvers\Audit;


use OwenIt\Auditing\Contracts\Auditable;

class IpAddressResolver implements \OwenIt\Auditing\Contracts\Resolver
{
    /**
     * {@inheritdoc}
     */
    public static function resolve(Auditable $auditable): string
    {
        return "";
    }
}