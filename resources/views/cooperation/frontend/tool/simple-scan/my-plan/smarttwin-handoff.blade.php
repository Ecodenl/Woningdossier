{{--
    SmartTwin SSO bridge.

    This tiny page exists because the handoff into SmartTwin must be a POST that
    carries the JWT in its body, and it must originate from the *user's* browser:
    only then do SmartTwin's login cookie and redirect land in the user's browser.
    A server-side POST or a normal redirect can't achieve "logged in + redirected".
    So we render a self-submitting form. No other UI belongs here.
--}}
<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    {{-- Do not leak the token via the Referer header to SmartTwin's page. --}}
    <meta name="referrer" content="no-referrer">
    <title>@lang('cooperation/frontend/tool.my-plan.smarttwin.redirecting')</title>
</head>
<body>
    <form id="smarttwin-sso" method="POST" action="{{ $url }}">
        <input type="hidden" name="token" value="{{ $token }}">
        <noscript>
            <p>@lang('cooperation/frontend/tool.my-plan.smarttwin.noscript')</p>
            <button type="submit">@lang('cooperation/frontend/tool.my-plan.smarttwin.continue')</button>
        </noscript>
    </form>

    <p>@lang('cooperation/frontend/tool.my-plan.smarttwin.redirecting')</p>

    <script @if(! empty($cspNonce)) nonce="{{ $cspNonce }}" @endif>
        document.getElementById('smarttwin-sso').submit();
    </script>
</body>
</html>
