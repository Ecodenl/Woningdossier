@lang('cooperation/mail/user/notify-resident-participant-added.salutation', [
     'name' => $user->getFullName(),
 ])
@php
    $cooperationWebsiteHref = is_null($userCooperation->cooperation_email) ? $userCooperation->website_url : $userCooperation->cooperation_email;
@endphp

@lang('cooperation/mail/user/notify-resident-participant-added.text', [
    'name' => $coach->getFullName(),
])

@lang('cooperation/mail/user/notify-resident-participant-added.any-questions', [
    'cooperation_link' => $cooperationWebsiteHref,
])

@lang('cooperation/mail/user/notify-resident-participant-added.kind_regards', ['app_name' => config('app.name')])