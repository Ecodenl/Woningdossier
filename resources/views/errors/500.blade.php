<div class="content">
    <div class="title">Page could not be found</div>

    @if(app()->bound('sentry') && !empty(Sentry::getLastEventID()))
        <div class="subtitle">Error ID: {{ Sentry::getLastEventID() }}</div>

        <!-- Sentry JS SDK 2.1.+ required -->
        <script src="https://cdn.ravenjs.com/3.3.0/raven.min.js"></script>

{{--        <script type="module" nonce="{{ $cspNonce }}">--}}
        <script type="module">
            @if (Auth::check())
            Raven.showReportDialog({
                eventId: '{{ Sentry::getLastEventID() }}',
                // use the public DSN (don't include your secret!)
                dsn: "{{ config('sentry.dsn') }}",
                user: {
                    'id': '{{ optional(\App\Helpers\Hoomdossier::user())->id }}'
                }
            });
            @else
            Raven.showReportDialog({
                eventId: '{{ Sentry::getLastEventID() }}',
                // use the public DSN (don't include your secret!)
                dsn: "{{ config('sentry.dsn') }}",
                extra: {
                    'visitor': 'true'
                }
            });
            @endif
        </script>
    @endif
</div>