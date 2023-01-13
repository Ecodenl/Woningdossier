@component('cooperation.pdf.user-report.components.new-page', ['id' => 'simple-scan-answers'])
    <h2>
        @lang('pdf/user-report.pages.simple-scan-answers.title')
    </h2>
    <p>
        @lang('pdf/user-report.pages.simple-scan-answers.text')
    </p>

    @foreach($simpleDump as $stepShort => $results)
        <div class="group">
            <h4>
                {{ \App\Models\Step::findByShort($stepShort)->name }}
            </h4>

            @include('cooperation.pdf.user-report.parts.step-summary')
        </div>
    @endforeach
@endcomponent