@lang('cooperation/mail/user/notify-coach-participant-added.salutation', [
     'name' => $coach->getFullName(),
 ])
@php
    $cooperationWebsiteHref = is_null($userCooperation->cooperation_email) ? $userCooperation->website_url : $userCooperation->cooperation_email;
@endphp

@lang('cooperation/mail/user/notify-coach-participant-added.text', [
    'name' => $user->getFullName(),
    'address' => $user->building->getAddress(),
])

@lang('cooperation/mail/user/notify-coach-participant-added.any-questions', [
    'cooperation_link' => $cooperationWebsiteHref,
])

@lang('cooperation/mail/user/notify-coach-participant-added.kind_regards', ['app_name' => config('app.name')])