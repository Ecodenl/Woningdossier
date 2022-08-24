<div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-{{$toolQuestions->count() > 1 ? 10 : 5}} ">
    @foreach($toolQuestions as $toolQuestion)
        @php
            $disabled = ! $building->user->account->can('answer', $toolQuestion);
            $humanReadableAnswer = null;

            switch($toolQuestion->short) {
                case 'building-type':
                    $rawAnswer = $building->getAnswer($masterInputSource, \App\Models\ToolQuestion::findByShort('building-type-category'));
                    // if there is an answer we can find the row and get the answer.
                    $model = \App\Models\BuildingTypeCategory::find($rawAnswer);
                    if ($model instanceof \App\Models\BuildingTypeCategory) {
                        $humanReadableAnswer = Str::lower($model->name);
                    }
                    break;
                default:
                    $humanReadableAnswer = null;
            }
        @endphp

        <div class="w-full @if($loop->iteration > 1) pt-10 @endif">
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'form-group-heading',
                // 'defaultInputSource' => 'resident',
                // so we give the option to replace something in the question title
                'label' => __($toolQuestion->name . (is_null($toolQuestion->forSpecificInputSource) ? '' : " ({$toolQuestion->forSpecificInputSource->name})"), ['name' => $humanReadableAnswer]),
                'inputName' => "filledInAnswers.{$toolQuestion->id}",
                'withInputSource' => ! $disabled,
            ])
                @slot('sourceSlot')
                    @include('cooperation.sub-step-templates.parts.source-slot-values', [
                        'values' => $filledInAnswersForAllInputSources[$toolQuestion->id],
                        'toolQuestion' => $toolQuestion,
                    ])
                @endslot

                @slot('modalBodySlot')
                    <p>
                        {!! $toolQuestion->help_text !!}
                    </p>
                @endslot

                @include("cooperation.tool-question-type-templates.{$toolQuestion->toolQuestionType->short}.show", [
                    'disabled' => $disabled,
                ])
            @endcomponent
        </div>
    @endforeach
</div>
