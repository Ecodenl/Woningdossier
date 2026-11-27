{{--
    The items of the account dropdown. Shared between the regular navbar and the SmartTwin navigation,
    so both modes offer the same options.
--}}
<li>
    <a href="{{ route('cooperation.my-account.index', compact('cooperation')) }}"
       class="in-text">
        @lang('woningdossier.cooperation.navbar.my-account')
    </a>

    @if(app()->isLocal() && $building instanceof \App\Models\Building)
        <p>
            B: {{ $building->id }}
            <br>
            U: {{ $building->user->id }}
        </p>
    @endif
</li>
<li>
    <a href="{{ route('cooperation.my-account.two-factor-authentication.index', compact('cooperation')) }}"
       class="in-text">
        @lang('woningdossier.cooperation.navbar.two-factor-authentication')
    </a>
</li>
<li>
    <a href="{{ route('cooperation.privacy.index', compact('cooperation')) }}"
       class="in-text">
        @lang('woningdossier.cooperation.navbar.privacy')
    </a>
</li>
<li>
    <a href="{{ route('cooperation.disclaimer.index', compact('cooperation')) }}"
       class="in-text">
        @lang('woningdossier.cooperation.navbar.disclaimer')
    </a>
</li>
<li>
    <a href="{{ route('cooperation.frontend.help.index', compact('cooperation')) }}"
       class="in-text">
        @lang('woningdossier.cooperation.navbar.help')
    </a>
</li>
{{--<li>--}}
{{--    <a href="{{ route('cooperation.my-account.cooperations.index', ['cooperation' => $cooperation->slug]) }}"--}}
{{--       class="in-text">--}}
{{--        @lang('my-account.cooperations.form.header')--}}
{{--    </a>--}}
{{--</li>--}}
<li>
    @include('cooperation.layouts.parts.logout')
</li>
