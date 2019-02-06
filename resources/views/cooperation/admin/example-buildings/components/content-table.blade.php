<?php

// determines form field array key (also used later on)
$fkey = $content instanceof \App\Models\ExampleBuildingContent ? $content->id : 'new';

// build year only
// full html array
$fname = 'content['.$fkey.'][build_year]';
$fvalKey = str_replace('[', '.', $fname);
// fallback value for old functions
$fallback = $content instanceof \App\Models\ExampleBuildingContent ? $content->build_year : '';

?>
<div class="form-group {{ $errors->has('content.'.$fkey.'.build_year') ? ' has-error' : '' }}">
    <label for="build_year">Build year:</label>


    <input id="build_year" type="number" min="0" name="content[{{ $fkey }}][build_year]"
           class="form-control" value="{{ App\Helpers\Old::get($fvalKey, $fallback) }}" />
    @if ($errors->has('content.'.$fkey.'.build_year'))
        <span class="help-block">
            <strong>{{ $errors->first('content.'.$fkey.'.build_year') }}</strong>
        </span>
    @endif
</div>


<table class="table table-responsive table-condensed">
    <thead>
    <tr>
        <th>Name</th>
        <th>Value</th>
    </tr>
    </thead>
    <tbody>
        @foreach($contentStructure as $step => $formFields)

            <tr>
                <td colspan="2">
                    <h3>{{ $step }}</h3>
                </td>
            </tr>

            @foreach($formFields as $formFieldName => $rowData)

	            <?php
                    // full html array
                    $fname = 'content['.$fkey.'][content]['.$step.']['.$formFieldName.']';
                    // laravel dotted notation
                    $fvalKey = str_replace(['[', ']'], ['.', ''], $fname);
                    // fallback value for old functions
                    $fallback = $content instanceof \App\Models\ExampleBuildingContent ? $content->getValue($step.'.'.$formFieldName) : '';
                ?>

            <tr>
                <td>
                    {!! $rowData['label'] !!}
                </td>
                <td>

                    <div class="form-group {{ $errors->has($fvalKey) ? ' has-error' : '' }}">

                    @if(isset($rowData['unit']))
                        <div class="input-group" >
                            <span class="input-group-addon">{!! $rowData['unit'] !!}</span>
                    @endif

                    @if($rowData['type'] == 'text')
                        <input type="text" class="form-control" name="{{ $fname }}" value="{{ App\Helpers\Old::get($fvalKey, $fallback) }}">
                        {{--<input type="text" class="form-control" name="content[@if($content instanceof \App\Models\ExampleBuildingContent){{ $content->id }}@endif][content][{{ $step }}][{{ $formFieldName }}]" value="@if($content instanceof \App\Models\ExampleBuildingContent){{ $content->getValue($step . '.'. $formFieldName) }}@endif">--}}
                    @elseif($rowData['type'] == 'select')

                        <select class="form-control" name="{{ $fname }}">
                            @foreach($rowData['options'] as $value => $label)
                                <option value="{{ $value }}" @if(App\Helpers\Old::get($fvalKey, $fallback) == $value)selected="selected"@endif>
                                    {{ $label }}
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
    </tbody>
</table>