{{--
    The woonplan before there is one. The check happens in SmartTwin, so this is an invitation to go
    there rather than a page of its own. Same layout as the filled woonplan, so the navigation does
    not move when the resident comes back with results.
--}}
@extends('cooperation.frontend.layouts.tool')

@section('content')
    @include('cooperation.frontend.tool.simple-scan.my-plan.parts.smarttwin-error')

    <div class="w-full flex justify-center">
        <div class="tile max-w-2xl text-center">
            <h3 class="tile-heading">
                @lang('cooperation/frontend/tool.my-plan.label')
            </h3>

            <div class="as-text">
                {!! __('cooperation/frontend/tool.my-plan.start-check.body') !!}
            </div>

            @if($canHandOff)
                {{-- New tab: whoever does the check leaves for another application and should find
                     the dossier still open when they come back. A failed hand-off redirects back to
                     the woonplan, which is still this page, so the error is shown above. --}}
                <form method="POST"
                      action="{{ route('cooperation.frontend.tool.simple-scan.my-plan.smarttwin', compact('scan')) }}"
                      target="_blank" rel="noopener"
                      class="tile-action">
                    @csrf
                    <button type="submit" class="btn btn-blue">
                        @lang('cooperation/frontend/tool.my-plan.smarttwin.button')
                    </button>
                </form>
            @else
                {{-- No SmartTwin account to send them to; the controller has reported it. --}}
                <p class="mt-6">
                    @lang('cooperation/frontend/tool.my-plan.smarttwin.errors.not_configured')
                </p>
            @endif
        </div>
    </div>
@endsection
