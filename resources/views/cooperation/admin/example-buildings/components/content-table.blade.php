<?php

// determines form field array key (also used later on)
$fkey = $content instanceof \App\Models\ExampleBuildingContent ? $content->id : 'new';

// build year only
// full html array
$fname = 'content['.$fkey.'][build_year]';
$fvalKey = str_replace(['[', ']'], ['.', ''], $fname);
// fallback value for old functions
$fallback = $content instanceof \App\Models\ExampleBuildingContent ? $content->build_year : '';

?>

@if(Route::currentRouteName() === "cooperation.admin.example-buildings.edit" && $fkey == 'new')
    <div class="alert alert-danger mt-3">
        @lang('cooperation/admin/example-buildings.edit.new-warning')
    </div>
@endif

<div class="form-group {{ $errors->has('content.'.$fkey.'.build_year') ? ' has-error' : '' }}">
    <label for="build_year">@lang('cooperation/admin/example-buildings.form.build-year')</label>


    <input id="build_year" type="number" min="0" name="content[{{ $fkey }}][build_year]"
           class="form-control" value="{{ old($fvalKey, $fallback) }}" />
    @if ($errors->has('content.'.$fkey.'.build_year'))
        <span class="help-block">
            <strong>{{ $errors->first('content.'.$fkey.'.build_year') }}</strong>
        </span>
    @endif
</div>


<table class="table table-responsive table-condensed">
    <thead>
    <tr>
        <th>@lang('cooperation/admin/example-buildings.form.field-name')</th>
        <th>@lang('cooperation/admin/example-buildings.form.field-value')</th>
    </tr>
    </thead>
    <tbody>
        @foreach($contentStructure as $step => $dataForSubSteps)
            <?php $stepName = \App\Models\Step::findByShort($step)->name ?>
            <tr>
                <td colspan="2">
                    <h3>{{$stepName}}</h3>
                </td>
            </tr>

            @foreach($dataForSubSteps as $subStep => $subStepData)
                <?php $possibleSubStep = \App\Models\Step::findByShort($subStep); ?>
                @if($possibleSubStep instanceof \App\Models\Step)
                <tr>
                    <td colspan="2">
                        <h4>{{$possibleSubStep->name}}</h4>
                    </td>
                </tr>
                @endif
                @foreach($subStepData as $formFieldName => $rowData)
                    @if($formFieldName != 'calculations' )
                        @include('cooperation.admin.example-buildings.parts.row-data')
                    @endif
                @endforeach
            @endforeach
        @endforeach
    </tbody>
</table>