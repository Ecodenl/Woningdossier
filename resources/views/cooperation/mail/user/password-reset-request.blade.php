@lang('mail.reset_password.why')
<br><br>

@lang('mail.reset_password.action')
<br>
<a href="{{ route('cooperation.auth.password.reset.show', ['cooperation' => $userCooperation, 'token' => $token, 'email' => $email]) }}">{{ route('cooperation.auth.password.reset.show', ['cooperation' => $userCooperation, 'token' => $token, 'email' => $email]) }}</a>
<br><br>
@lang('mail.reset_password.not_requested')

<br><br>
@lang('mail.password_reset.kind_regards', ['app_name' => config('app.name')])