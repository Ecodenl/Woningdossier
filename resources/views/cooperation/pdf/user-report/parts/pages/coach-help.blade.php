@component('cooperation.pdf.user-report.components.new-page', ['id' => 'coach-help'])
    <h4>
        @lang('pdf/user-report.pages.coach-help.title')
    </h4>

    <div class="group">
        @php
            $results = $coachHelp;
            $stepShort = 'small-measures-lite';
        @endphp
        @include('cooperation.pdf.user-report.parts.step-summary')
    </div>
@endcomponent