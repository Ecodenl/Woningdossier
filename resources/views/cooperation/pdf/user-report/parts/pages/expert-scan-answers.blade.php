@component('cooperation.pdf.user-report.components.new-page', ['id' => 'expert-scan-answers'])
    <h1 class="my-2">
        @lang('pdf/user-report.pages.expert-scan-answers.title')
    </h1>
    <p>
        @lang('pdf/user-report.pages.expert-scan-answers.text')
    </p>

    <div class="group">
        <h4>
            @lang('pdf/user-report.pages.expert-scan-answers.action-plan')
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


    @foreach($expertDump as $stepShort => $results)
        @php
            $step = \App\Models\Step::findByShort($stepShort);

            // Don't show current step if the user has no measure for it.
            if (! in_array($step->id, $measureSteps)) {
                continue;
            }
        @endphp

        @include('cooperation.pdf.user-report.parts.page-break')

        @include('cooperation.pdf.user-report.parts.step-intro')

        <div class="group">
            @include('cooperation.pdf.user-report.parts.step-summary')
        </div>
    @endforeach
@endcomponent