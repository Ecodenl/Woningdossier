<?php
    $optionalValidationRule = '';
    $mainValidationRule = '';

    if (is_array($question->validation)) {
        $mainValidationRule = key($question->validation);
        $optionalValidationRule = key($question->validation[$mainValidationRule]);
    }
?>

<div class="col-sm-4">
    <div class="form-group">
        <select class="form-control validation" name="questions[edit][{{$question->id}}][validation]" id="">
            @foreach(__('woningdossier.cooperation.admin.custom-fields.index.rules') as $rule => $translation)
                <option @if($mainValidationRule == $rule) selected @endif value="{{$rule}}">{{$translation}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-sm-4">
    <div class="form-group">
        @foreach(__('woningdossier.cooperation.admin.custom-fields.index.rules') as $rule => $translation)
            <select class="form-control validation-options" name="questions[edit][{{$question->id}}][validation-options]" @if($rule != $mainValidationRule) style="display: none;" @endif id="{{$rule}}">
                @foreach(__('woningdossier.cooperation.admin.custom-fields.index.optional-rules.'.$rule) as $optionalRule => $optionalRuleTranslation)
                    <option @if($optionalValidationRule == $optionalRule) selected @endif value="{{$optionalRule}}">{{$optionalRuleTranslation}}</option>
                @endforeach
            </select>
        @endforeach
    </div>
</div>

@if(is_array($question->validation))
    @foreach($question->validation[$mainValidationRule] as $optionalRule => $rules)
        @foreach($rules as $validationOptionName => $validationOptionValue)
            <div class="col-sm-2">
                <div class="form-group">
                    <input type="text" name="questions[new][{{$question->id}}][validation-options][{{$optionalRule}}][{{$validationOptionName}}]" class="form-control" value="{{$validationOptionValue}}">
                </div>
            </div>
        @endforeach
    @endforeach
@endif