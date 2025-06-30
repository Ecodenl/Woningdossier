@php
    $buildYear = $content->build_year ?? 'new';
@endphp

@if($buildYear == 'new')
    <div class="p-4">
        @if(isset($exampleBuilding))
            @component('cooperation.layouts.components.alert', ['color' => 'red', 'dismissible' => false])
                @lang('cooperation/admin/example-buildings.edit.new-warning')
            @endcomponent
        @endif

        @component('cooperation.frontend.layouts.components.form-group', [
            'class' => 'w-full sm:w-1/2',
            'label' => __('cooperation/admin/example-buildings.form.build-year'),
            'id' => 'build-year',
            'inputName' => 'contents.new.build_year',
            'withInputSource' => false,
        ])
            <input id="build_year" type="number" min="1800" wire:model.live.debounce.500ms="contents.new.build_year" class="form-input"/>
        @endcomponent
    </div>
@endif

<table class="table simple-table">
    <thead>
        <tr>
            <th>@lang('cooperation/admin/example-buildings.form.field-name')</th>
            <th>@lang('cooperation/admin/example-buildings.form.field-value')</th>
        </tr>
    </thead>
    <tbody>
        @foreach($exampleBuildingSteps as $step)
            <tr>
                <td colspan="2">
                    <h2 class="heading-3">
                        {{$step->name}}
                    </h2>
                </td>
            </tr>
            @foreach($step->subSteps as $subStep)
                @if($subStep->toolQuestions->isNotEmpty())
                    <tr>
                        <td colspan="2">
                            <h4 class="heading-4">
                                {{$subStep->name}}
                            </h4>
                        </td>
                    </tr>
                    @foreach($subStep->toolQuestions as $toolQuestion)
                        <tr>
                            <td>
                                {{$toolQuestion->name}}
                                @if(isset($toolQuestion->options['min']))
                                    <small>
                                        Minimaal {{$toolQuestion->options['min']}}
                                    </small>
                                @endif
                                @if(isset($toolQuestion->options['max']))
                                    <small>
                                        Maximaal {{$toolQuestion->options['max']}}
                                    </small>
                                @endif
                            </td>
                            <td>

                                @php
                                    $inputName = ['contents', $buildYear, $toolQuestion->short];
                                    $select = false;
                                    $multiple = false;
                                    if (in_array($toolQuestion->pivot->toolQuestionType->short, ['radio-icon', 'radio-icon-small', 'radio', 'dropdown'])) {
                                        $select = true;
                                    }

                                    if (in_array($toolQuestion->pivot->toolQuestionType->short, ['checkbox-icon', 'multi-dropdown'])) {
                                        $select = true;
                                        $multiple = true;
                                        // $inputName[] = '*';
                                    }

                                    $inputName = implode('.', $inputName);
                                @endphp

                                @component('cooperation.frontend.layouts.components.form-group', [
                                    'class' => 'w-full -mt-5 sm:w-1/2',
                                    'id' => Str::slug($inputName),
                                    'inputName' => $inputName,
                                    'withInputSource' => false,
                                ])
                                    @if($select)
                                        @php
                                            $questionValues = \App\Helpers\QuestionValues\QuestionValue::init($cooperation, $toolQuestion)
                                                ->answers(collect($contents[$buildYear]))
                                                ->getQuestionValues();
                                        @endphp

                                        @component('cooperation.frontend.layouts.components.alpine-select', [
                                            'append' => $toolQuestion->unit_of_measure
                                        ])
                                            <select class="form-input hidden @if(! empty($toolQuestion->unit_of_measure)) with-append @endif"
                                                    wire:model.live="{{$inputName}}"
                                                    @if($multiple) multiple="multiple" @endif >
                                                <option value="" selected @if($multiple) disabled @endif>-</option>
                                                @foreach($questionValues as $toolQuestionValue)
                                                    <option value="{{ $toolQuestionValue['value'] }}">
                                                        {{ $toolQuestionValue['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endcomponent
                                    @else
                                        <input type="text" class="form-input mb-0 @if(! empty($toolQuestion->unit_of_measure)) with-append @endif"
                                               wire:model.blur="{{$inputName}}">

                                        @if(! empty($toolQuestion->unit_of_measure))
                                            <div class="input-group-append mb-0">
                                                {{$toolQuestion->unit_of_measure}}
                                            </div>
                                        @endif
                                    @endif
                                @endcomponent
                            </td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        @endforeach
    </tbody>
</table>