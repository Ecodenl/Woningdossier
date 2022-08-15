@extends('cooperation.frontend.layouts.tool')

@section('step_title', $step->name)

@section('content')
    <livewire:cooperation.tool.expert-scan.form :step="$step" />
    @foreach($step->subSteps as $subStep)
        <h4>{{$subStep->name}}</h4>
        <div class="flex justify-between">
            @foreach($subStep->toolQuestions as $toolQuestion)

                @php
                    $disabled = ! $building->user->account->can('answer', $toolQuestion);
                    $humanReadableAnswer = null;

                switch($toolQuestion->short) {
                    case 'building-type':
                        $rawAnswer = $building->getAnswer($masterInputSource, \App\Models\ToolQuestion::findByShort('building-type-category'));
                        // if there is an answer we can find the row and get the answer.
                        $model = \App\Models\BuildingTypeCategory::find($rawAnswer);
                        if ($model instanceof \App\Models\BuildingTypeCategory) {
                            $humanReadableAnswer = Str::lower(
                             $model->name
                            );
                        }
                        break;
                    default:
                        $humanReadableAnswer = null;
                }
                @endphp



                <div class="{{$toolQuestion->pivot->size}}">
                    @component('cooperation.frontend.layouts.components.form-group', [
                              'class' => 'form-group-heading',
                              // 'defaultInputSource' => 'resident',
                              // so we give the option to replace something in the question title
                              'label' => __($toolQuestion->name . (is_null($toolQuestion->forSpecificInputSource) ? '' : " ({$toolQuestion->forSpecificInputSource->name})"), ['name' => $humanReadableAnswer]),
                              'inputName' => "filledInAnswers.{$toolQuestion->id}",
                              'withInputSource' => ! $disabled,
                          ])
                        @slot('sourceSlot')
                        @endslot

                        @slot('modalBodySlot')
                            <p>
                                {!! $toolQuestion->help_text !!}
                            </p>
                        @endslot
                    @include("cooperation.tool-question-type-templates.{$toolQuestion->toolQuestionType->short}.show", [
                      'disabled' => $disabled ?? false,
                    ])
                    @endcomponent
                </div>
            @endforeach
        </div>
    @endforeach
@endsection

