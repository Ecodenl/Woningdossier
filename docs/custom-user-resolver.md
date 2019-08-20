# User resolving

Lately, a change has been made so users can be associated with multiple 
cooperations, but they can log in with the same credentials. After logging in, 
apart from the `Account` model there are no "shared" models any longer at all.
This is best described by the 'before' and 'after' situations:

## The situation before:

`user -> cooperation_user -> cooperation`

This has two (unwanted) downsides once a user is member of multiple cooperations.
Both downsides have the most impact on the end user and the user's "understanding" 
of this tool:
 
- There was only one User model. Changing data here would mean that data would be 
changed for every cooperation the user is a member of. This also holds for the 
user's energy habits. Both these things can be handy and technically seen data 
would stay 'more sync', but it's not transparent to the user which data is 
"shared" between cooperations and which not,
- The user was attached to one building, so in this case changing building details 
would change those for every cooperation. The only solution for that could have 
been to pull building id into the cooperation_user relationship. That would 
make the building silo'ed per cooperation, but not the user.. 

## The situation now:

`account -> user -> cooperation`

What is done:

- The `Authenticatable` and `Authorizable` are pulled apart. In the default case 
Laravel couples these in the `User` model. Now, the `Account` model is 
`Authenticatable` and the User model is `Authorizable` (as a user has roles and 
might have different roles per cooperation)
- There's a *custom user resolver* in `\App\Providers\AuthServiceProvider` which 
was needed for injecting the actual `User` in the gates and policies.

This has the following implication:

`\Auth::user()` no longer returns a `User` model, as that method will return the 
`Authenticatable` model. Therefore it will return an `Account` model.

As this might lead to confusion for contributors, partly driven by the habit to 
use `\Auth::user()` we have removed the usage of `\Auth::user()` in the codebase.
This is replaced by helper functions `\App\Helpers\Hoomdossier::user()` and 
`\App\Helpers\Hoomdossier::account()` to make the code more readable, 
understandable and to resist the urge to use the `Auth` facade in the future 
which would result in `\Auth::user()->user()` all over the place.