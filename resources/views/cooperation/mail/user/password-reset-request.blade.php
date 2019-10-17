@lang('mail.reset_password.why')
<br><br>

@lang('mail.reset_password.action')
<br>
<a href="{{ route('cooperation.password.reset', ['cooperation' => $userCooperation, 'token' => $token]) }}">{{ route('cooperation.password.reset', ['cooperation' => $userCooperation, 'token' => $token]) }}</a>
<br><br>
@lang('mail.reset_password.not_requested')

<br><br>
@lang('mail.account-created-by-cooperation.kind_regards', ['app_name' => config('app.name')])