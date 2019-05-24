# Middleware's for Hoomdossier
As in most projects, we use middleware's to keep unauthorized requests out the door.

## RoleMiddleware
The ``RoleMiddleware`` will check if the user has a role, if so it will authorize the request.

## CurrentRoleMiddleware
The ``CurrentRoleMiddleware`` will check if the user has the given roles **and** check if one of the given roles
has been set in the [HoomdossierSession](session-handling.md).