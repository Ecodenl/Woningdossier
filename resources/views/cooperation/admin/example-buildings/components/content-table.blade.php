@php
    $buildYear = $content->build_year ?? 'new';
@endphp

@if($buildYear == 'new')
    <div class="alert alert-danger mt-3">
        @lang('cooperation/admin/example-buildings.edit.new-warning')
    </div>

    <div class="form-group {{ $errors->has('contents.new.build_year') ? ' has-error' : '' }}">
        <label for="build_year">@lang('cooperation/admin/example-buildings.form.build-year')</label>


        <input id="build_year" type="number" min="0" wire:model="contents.new.build_year" class="form-control"/>
        @if ($errors->has('contents.new.build_year'))
            <span class="help-block">
            <strong>{{ $errors->first('contents.new.build_year') }}</strong>
        </span>
        @endif
    </div>

@endif

<table class="table table-responsive table-condensed">
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
                <h2>{{$step->name}}</h2>
            </td>
        </tr>
        @foreach($step->subSteps as $subStep)
            @if($subStep->toolQuestions->isNotEmpty())
                <tr>
                    <td colspan="2">
                        <h4>{{$subStep->name}}</h4>
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
                                if(in_array($toolQuestion->pivot->toolQuestionType->short, ['radio-icon', 'radio-icon-small', 'radio', 'dropdown'])) {
                                    $select = true;
                                }

                                if(in_array($toolQuestion->pivot->toolQuestionType->short, ['checkbox-icon', 'multi-dropdown'])) {
                                    $select = true;
                                    $multiple = true;
                                    // $inputName[] = '*';
                                }

                                $inputName = implode('.', $inputName);
                            @endphp
                            <div class="form-group {{ $errors->has($inputName) ? ' has-error' : '' }}">

                                @if(!empty($toolQuestion->unit_of_measure))
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$toolQuestion->unit_of_measure}}</span>
                                        @endif
                                        @if($select)
                                            @php
                                                $questionValues = \App\Helpers\QuestionValues\QuestionValue::init($cooperation, $toolQuestion)
                                                                   ->answers(collect($contents[$buildYear]))
                                                                   ->getQuestionValues();
                                            @endphp
                                            <select class="form-control" wire:model="{{$inputName}}"
                                                    @if($multiple) multiple="multiple" @endif >
                                                <option value="null">-</option>
                                                @foreach($questionValues as $toolQuestionValue)
                                                    <option  value="{{ $toolQuestionValue['value'] }}">
                                                        {{ $toolQuestionValue['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="text" class="form-control" wire:model.lazy="{{$inputName}}">
                                        @endif

                                        @if(isset($rowData['unit']))
                                    </div>
                                @endif

                                @if ($errors->has($inputName))
                                    <span class="help-block">
                        <strong>{{ $errors->first($inputName) }}</strong>
                    </span>
                                @endif

                            </div>
                        </td>
                    </tr>
                @endforeach
            @endif
        @endforeach
    @endforeach
    </tbody>
</table>