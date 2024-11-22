<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class RoleInSessionHasNoAssociationWithUser extends HttpException
{
    public static function forRole($role): static
    {
        $message = "Role ({$role->name}) in session has no association with the user.";

        return new static(403, $message, null, []);
    }
}
