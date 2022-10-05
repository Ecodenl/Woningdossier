{{--@if(Route::currentRouteName() === "cooperation.admin.example-buildings.edit" && $fkey == 'new')--}}
{{--    <div class="alert alert-danger mt-3">--}}
{{--        @lang('cooperation/admin/example-buildings.edit.new-warning')--}}
{{--    </div>--}}
{{--@endif--}}

{{--<div class="form-group {{ $errors->has('content.'.$fkey.'.build_year') ? ' has-error' : '' }}">--}}
{{--    <label for="build_year">@lang('cooperation/admin/example-buildings.form.build-year')</label>--}}


{{--    <input id="build_year" type="number" min="0" name="content[{{ $fkey }}][build_year]"--}}
{{--           class="form-control" value="{{ old($fvalKey, $fallback) }}" />--}}
{{--    @if ($errors->has('content.'.$fkey.'.build_year'))--}}
{{--        <span class="help-block">--}}
{{--            <strong>{{ $errors->first('content.'.$fkey.'.build_year') }}</strong>--}}
{{--        </span>--}}
{{--    @endif--}}
{{--</div>--}}

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
                <h4>{{$step->name}}</h4>
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
                    $fvalKey = $content->build_year.$subSteppablePivot->subSteppable;
                @endphp

                <tr>
                    <td>
                        {{$subStep->name}}
                    </td>
                    <td>

                        <div class="form-group {{ $errors->has($fvalKey) ? ' has-error' : '' }}">

                            @if(!empty($subSteppable->unit_of_measure))
                                <div class="input-group">
                                    <span class="input-group-addon">{{$subSteppable->unit_of_measure}}</span>
                                    @endif
                                    @php
                                        $select = false;
                                        $multiple = false;
                                        if(in_array($subSteppablePivot->toolQuestionType->short, ['radio-icon', 'radio-icon-small', 'radio', 'dropdown'])) {
                                            $select = true;
                                        }
                                        if(in_array($subSteppablePivot->toolQuestionType->short, ['checkbox-icon', 'multi-dropdown'])) {
                                            $select = true;
                                            $multiple = true;
                                        }
                                    @endphp

                                    @if($select)
                                        @php
                                            $questionValues = \App\Helpers\QuestionValues\QuestionValue::init($cooperation, $subSteppable)
                                                               ->answers(collect($contents[$content->build_year]))
                                                               ->getQuestionValues();
                                        @endphp
                                        <select class="form-control" name="contents.{{$content->build_year}}.{{$subSteppable->short}}" @if($multiple) multiple="multiple" @endif >
                                            @foreach($questionValues as $toolQuestionValue)
                                                <option value="{{ $toolQuestionValue['value'] }}">
                                                    {{ $toolQuestionValue['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
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
    {{--        @foreach($contentStructure as $step => $dataForSubSteps)--}}
    {{--            <?php $stepName = \App\Models\Step::findByShort($step)->name ?? __('cooperation/admin/example-buildings.form.general-data') ?>--}}
    {{--            <tr>--}}
    {{--                <td colspan="2">--}}
    {{--                    <h3>{{$stepName}}</h3>--}}
    {{--                </td>--}}
    {{--            </tr>--}}

    {{--            @foreach($dataForSubSteps as $subStep => $subStepData)--}}
    {{--                <?php $possibleSubStep = \App\Models\Step::findByShort($subStep); ?>--}}
    {{--                @if($possibleSubStep instanceof \App\Models\Step)--}}
    {{--                    <tr>--}}
    {{--                        <td colspan="2">--}}
    {{--                            <h4>{{$possibleSubStep->name}}</h4>--}}
    {{--                        </td>--}}
    {{--                    </tr>--}}
    {{--                @endif--}}
    {{--                @foreach($subStepData as $formFieldName => $rowData)--}}
    {{--                    @if($formFieldName != 'calculations' )--}}
    {{--                        @include('cooperation.admin.example-buildings.parts.row-data')--}}
    {{--                    @endif--}}
    {{--                @endforeach--}}
    {{--            @endforeach--}}
    {{--        @endforeach--}}
    </tbody>
</table>