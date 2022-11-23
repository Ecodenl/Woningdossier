@lang('cooperation/mail/reset-password.salutation', [
    'first_name' => $user->first_name,
    'last_name' => $user->last_name
])
<br>
<br>
@lang('cooperation/mail/reset-password.why')

<br>
<br>
<a href="{{route('cooperation.auth.password.reset', ['cooperation' => $userCooperation, 'token' => $token])}}">
    @lang('cooperation/mail/reset-password.button')
</a>
<br>
<br>
@lang('cooperation/mail/reset-password.not_requested')
<br>
<br>
@lang('cooperation/mail/reset-password.kind_regards', ['app_name' => config('app.name')])