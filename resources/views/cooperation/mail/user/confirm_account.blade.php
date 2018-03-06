@lang('mail.confirm_account.salutation', ['first_name' => $user->first_name, 'last_name' => $user->last_name])

@lang('mail.confirm_account.text', ['home_url' => config('app.url'), 'confirm_url' => route('cooperation.confirm', ['cooperation' => $cooperation, 'u' => $user->email, 't' => $user->confirm_token])])

@lang('mail.confirm_account.signature', ['app_name' => config('app.name')])