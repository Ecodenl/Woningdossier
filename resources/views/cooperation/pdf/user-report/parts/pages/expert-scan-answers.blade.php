@component('cooperation.pdf.user-report.components.new-page', ['id' => 'expert-scan-answers'])
    <h2>
        @lang('pdf/user-report.expert-scan-answers.title')
    </h2>
    <p>
        @lang('pdf/user-report.expert-scan-answers.text')
    </p>

    <div class="group">
        <h4>
            @lang('pdf/user-report.expert-scan-answers.action-plan')
        </h4>
        <p>
            @foreach($categorizedAdvices as $category => $advices)
                @if($category !== \App\Services\UserActionPlanAdviceService::CATEGORY_COMPLETE)
                    @foreach($advices as $advice)
                        {{ $advice->userActionPlanAdvisable->name }}
                        <br>
                    @endforeach
                @endif
            @endforeach
        </p>
    </div>

    @include('cooperation.pdf.user-report.parts.page-break')

    @foreach($expertDump as $stepShort => $results)
        @php
            $step = \App\Models\Step::findByShort($stepShort);

            // Don't show current step if the user has not yet completed it
            if (! $building->hasCompleted($step, $inputSource)) {
                continue;
            }
        @endphp

        @include('cooperation.pdf.user-report.parts.step-intro')

        @include('cooperation.pdf.user-report.parts.step-summary')

        @include('cooperation.pdf.user-report.parts.page-break')
    @endforeach
@endcomponent