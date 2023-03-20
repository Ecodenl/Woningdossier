# Hoomdossier docs

This folder contains some important decisions during the development of this 
tool. We tend to document the implementations and their consequences because of 
 business logic decisions (where they would have been implemented  differently 
 in `the ideal world`)

## Good to knows / guidelines

- We use a [custom user resolver](./code-logic/custom-user-resolver.md). Use `\App\Helpers\Hoomdossier::user()` instead of `\Auth::user()`
- There's a [custom session handler](./code-logic/session-handling.md) in `\App\Helpers\HoomdossierSession`
- Password reset token validity: 30 days (`config/auth.php`). Config is done in minutes.