@component('cooperation.pdf.components.new-page')
    <div class="container">
        <div class="question-answer-section">
            <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.address-info.title')}}</p>
            <div class="question-answer">
                <p class="w-380">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.address-info.name')}}</p>
                <p>{{$user->getFullName()}}</p>
            </div>
            <div class="question-answer">
                <p class="w-380">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.address-info.address')}}</p>
                <p>{{$building->street}} {{$building->number}} {{$building->extension}}</p>
            </div>
            <div class="question-answer">
                <p class="w-380">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.address-info.zip-code-city')}}</p>
                <p>{{$building->postal_code}} {{$building->city}}</p>
            </div>
        </div>


        @php

            $summaryStep =  \App\Models\Step::findByShort('building-data');
            $summarySubStepOrder = $summaryStep->subSteps()->max('order');

            $subStepsToSummarize = $summaryStep->subSteps()->where('order', '<', $summarySubStepOrder)->orderBy('order')->get();

        @endphp

        @include('cooperation.pdf.user-report.parts.step-summary', compact('subStepsToSummarize', 'summaryStep'))


        @php

            $summaryStep =  \App\Models\Step::findByShort('residential-status');
            $summarySubStepOrder = $summaryStep->subSteps()->max('order');

            $subStepsToSummarize = $summaryStep->subSteps()->where('order', '<', $summarySubStepOrder)->orderBy('order')->get();

        @endphp

        @include('cooperation.pdf.user-report.parts.step-summary', compact('subStepsToSummarize', 'summaryStep'))

    </div>
@endcomponent



@component('cooperation.pdf.components.new-page')
    <div class="container">

        @php

            $summaryStep =  \App\Models\Step::findByShort('usage-quick-scan');
            $summarySubStepOrder = $summaryStep->subSteps()->max('order');

            $subStepsToSummarize = $summaryStep->subSteps()->where('order', '<', $summarySubStepOrder)->orderBy('order')->get();

        @endphp

        @include('cooperation.pdf.user-report.parts.step-summary', compact('subStepsToSummarize', 'summaryStep'))



        @php
            $summaryStep =  \App\Models\Step::findByShort('living-requirements');
        @endphp
        <div class="question-answer-section">
            <p class="lead">
                {{$summaryStep->name}}
            </p>
            @if(isset($commentsByStep[$summaryStep->short]['-']))
                @include('cooperation.pdf.user-report.parts.measure-page.comments', [
                    'title' => __('pdf/user-report.general-data.comment'),
                    'comments' => $commentsByStep[$summaryStep->short]['-'],
                ])
            @endif
        </div>
    </div>
@endcomponent



