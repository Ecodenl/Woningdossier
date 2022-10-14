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
            <tr>
                <td colspan="2">
                    <h4>{{$subStep->name}}</h4>
                </td>
            </tr>
            @foreach($subStep->subSteppables as $subSteppablePivot)
                @php
                    $subSteppable = $subSteppablePivot->subSteppable;
                @endphp
                @if(! in_array($subSteppable->short, $hideTheseToolQuestions))
                    <tr>
                        <td>
                            {{$subSteppable->name}}
                        </td>
                        <td>

                            @php
                                $inputName = ['contents', $buildYear, $subSteppable->short];
                                $select = false;
                                $multiple = false;
                                if(in_array($subSteppablePivot->toolQuestionType->short, ['radio-icon', 'radio-icon-small', 'radio', 'dropdown'])) {
                                    $select = true;
                                }

                                if(in_array($subSteppablePivot->toolQuestionType->short, ['checkbox-icon', 'multi-dropdown'])) {
                                    $select = true;
                                    $multiple = true;
                                    // $inputName[] = '*';
                                }

                                $inputName = implode('.', $inputName);
                            @endphp
                            <div class="form-group {{ $errors->has($inputName) ? ' has-error' : '' }}">

                                @if(!empty($subSteppable->unit_of_measure))
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$subSteppable->unit_of_measure}}</span>
                                        @endif
                                        @if($select)
                                            @php
                                                $questionValues = \App\Helpers\QuestionValues\QuestionValue::init($cooperation, $subSteppable)
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
                                            <input type="text" class="form-control" wire:model="{{$inputName}}">
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
                @endif
            @endforeach
        @endforeach
    @endforeach
    </tbody>
</table>