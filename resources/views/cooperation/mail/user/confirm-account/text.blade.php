@php
    $cooperationHoomdossierHref = route('cooperation.home', ['cooperation' => $userCooperation]);

    $cooperationWebsiteHref = is_null($userCooperation->cooperation_email) ? $userCooperation->website_url : $userCooperation->cooperation_email;

    $customMail = \App\Helpers\Models\CooperationSettingHelper::getSettingValue($userCooperation, \App\Helpers\Models\CooperationSettingHelper::SHORT_VERIFICATION_EMAIL_TEXT);

    if (! is_null($customMail)) {
        $customMail = \App\Helpers\Str::of($customMail)->replace(':first_name', $user->first_name)
            ->replace(':last_name', $user->last_name)
            ->replace(':hoomdossier_link', $cooperationHoomdossierHref)
            ->replace(':verify_link', __('cooperation/mail/confirm-account.button') . ': ' . $verifyUrl)
            ->replace('cooperation_link', $cooperationWebsiteHref)
            ->__toString();
    }
@endphp

@if(! empty($customMail))
    {!! $customMail !!}
@else
    @lang('cooperation/mail/confirm-account.salutation', [
         'first_name' => $user->first_name,
         'last_name' => $user->last_name
     ])

    @lang('cooperation/mail/confirm-account.text', [
        'hoomdossier_link' => $cooperationHoomdossierHref
    ])

    @lang('cooperation/mail/confirm-account.button'): {!! $verifyUrl !!}

    @lang('cooperation/mail/confirm-account.any-questions', ['cooperation_link' => $cooperationWebsiteHref])

    @lang('cooperation/mail/confirm-account.kind_regards', ['app_name' => config('app.name')])
@endif