
<table class="table table-responsive table-condensed">
    <thead>
    <tr>
        <th>@lang('cooperation/admin/example-buildings.form.field-name')</th>
        <th>@lang('cooperation/admin/example-buildings.form.field-value')</th>
    </tr>
    </thead>
    <tbody>
    @php
        $buildYear = $content->build_year ?? 'new';
    @endphp
    @foreach($exampleBuildingSteps as $step)
        @if($buildYear === "new" && $loop->first)
        <tr>
            <td>
                Bouw jaar
            </td>
            <td>
                <div class="form-group">
                    <input type="text" class="form-control" wire:model="contents.new.build_year">
                </div>
            </td>
        </tr>
        @endif
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
                    $fvalKey = $buildYear.$subSteppablePivot->subSteppable;
                @endphp

                <tr>
                    <td>
                        {{$subSteppable->name}}
                    </td>
                    <td>

                        <div class="form-group {{ $errors->has($fvalKey) ? ' has-error' : '' }}">

                            @if(!empty($subSteppable->unit_of_measure))
                                <div class="input-group">
                                    <span class="input-group-addon">{{$subSteppable->unit_of_measure}}</span>
                                    @endif
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
                                            $inputName[] = '*';
                                        }

                                        $inputName = implode('.', $inputName);
                                    @endphp

                                    @if($select)
                                        @php
                                            $questionValues = \App\Helpers\QuestionValues\QuestionValue::init($cooperation, $subSteppable)
                                                               ->answers(collect($contents[$buildYear]))
                                                               ->getQuestionValues();
                                        @endphp
                                        <select class="form-control"
                                                wire:model  ="{{$inputName}}"
                                                @if($multiple) multiple="multiple" @endif >
                                            @foreach($questionValues as $toolQuestionValue)
                                                <option value="{{ $toolQuestionValue['value'] }}">
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

                            @if ($errors->has($fvalKey))
                                <span class="help-block">
                        <strong>{{ $errors->first($fvalKey) }}</strong>
                    </span>
                            @endif

                        </div>
                    </td>
                </tr>
            @endforeach
        @endforeach
    @endforeach
    </tbody>
</table>