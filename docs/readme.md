# Hoomdossier docs

This folder contains some important decisions during the development of this 
tool. We tend to document the implementations and their consequences because of 
 business logic decisions (where they would have been implemented  differently 
 in `the ideal world`)

## Good to knows / guidelines

- We use a [custom user resolver](./custom-user-resolver.md). Use `\App\Helpers\Hoomdossier::user()` instead of `\Auth::user()`
- There's a [custom session handler](./session-handling.md) in `\App\Helpers\HoomdossierSession`