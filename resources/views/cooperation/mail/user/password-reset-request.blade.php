@component('mail::message')

@lang('cooperation/mail/reset-password.salutation', [
    'first_name' => $user->first_name,
    'last_name' => $user->last_name
])
<br>
<br>
@lang('cooperation/mail/reset-password.why')

@component('mail::button', ['url' => route('cooperation.auth.password.reset.show', ['cooperation' => $userCooperation, 'token' => $token, 'email' => $email])])
    @lang('cooperation/mail/reset-password.button')
@endcomponent

@lang('cooperation/mail/reset-password.button-does-not-work')
<br>
<a href="{{ route('cooperation.auth.password.reset.show', ['cooperation' => $userCooperation, 'token' => $token, 'email' => $email]) }}">{{ route('cooperation.auth.password.reset.show', ['cooperation' => $userCooperation, 'token' => $token, 'email' => $email]) }}</a>
<br>
<br>
@lang('cooperation/mail/reset-password.not_requested')

<br><br>
@lang('cooperation/mail/reset-password.kind_regards', ['app_name' => config('app.name')])

@endcomponent