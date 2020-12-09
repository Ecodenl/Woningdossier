<?php
    // full html array
    $fname = 'content['.$fkey.'][content]['.$step.']['.$subStep.']['.$formFieldName.']';
    // laravel dotted notation
    $fvalKey = str_replace(['[', ']'], ['.', ''], $fname);

    // fallback value for old functions

    $getValueKey = $step.'.'.$subStep.'.'.$formFieldName;

    $fallback = $content instanceof \App\Models\ExampleBuildingContent ? $content->getValue($getValueKey) : '';
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
                        <input type="text" class="form-control" name="{{ $fname }}" value="{{ App\Helpers\Views\ExampleBuildingOldHelper::old($fvalKey, $fallback) }}">
                        {{--<input type="text" class="form-control" name="content[@if($content instanceof \App\Models\ExampleBuildingContent){{ $content->id }}@endif][content][{{ $step }}][{{ $formFieldName }}]" value="@if($content instanceof \App\Models\ExampleBuildingContent){{ $content->getValue($step . '.'. $formFieldName) }}@endif">--}}
                    @elseif($rowData['type'] == 'select')

                        <select class="form-control" name="{{ $fname }}">
                            @foreach($rowData['options'] as $value => $label)
                                <option value="{{ $value }}" @if(App\Helpers\Views\ExampleBuildingOldHelper::old($fvalKey, $fallback) == $value)selected="selected"@endif>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    @elseif($rowData['type'] == 'multiselect')
                        <?php
                        if (empty($fallback)){
                            $fallback = [];
                        }
                        elseif(!is_array($fallback)){
                            $fallback = [ $fallback ];
                        }
                        ?>
                        <select class="form-control" name="{{ $fname }}[]" multiple>
                            @foreach($rowData['options'] as $value => $label)
                                <option value="{{ $value }}" @if(in_array($value, App\Helpers\Views\ExampleBuildingOldHelper::old($fvalKey, $fallback)))selected="selected"@endif>
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