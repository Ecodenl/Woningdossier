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
                    <h3>{{ \App\Helpers\Translation::translate('woningdossier.cooperation.tool.' . $step . '.title') }}</h3>
                </td>
            </tr>

            @foreach($formFields as $stepShortOrFormFieldName => $stepDataOrRowData)
                <?php $possibleSubStep = \App\Models\Step::findByShort($stepShortOrFormFieldName); ?>
                @if ($possibleSubStep instanceof \App\Models\Step)
                    <tr>
                        <td colspan="2">
                            <h4>{{ $possibleSubStep->name }}</h4>
                        </td>
                    </tr>
                    @foreach($stepDataOrRowData as $formFieldName => $rowData)
                        @if($formFieldName != 'calculations' )
                            @include('cooperation.admin.example-buildings.parts.row-data')
                        @endif
                    @endforeach
                @else
                    @if($stepShortOrFormFieldName != 'calculations' )
                        @include('cooperation.admin.example-buildings.parts.row-data', [
                            'rowData' => $stepDataOrRowData,
                            'formFieldName' => $stepShortOrFormFieldName
                        ])
                    @endif
                @endif
            @endforeach
        @endforeach
    </tbody>
</table>