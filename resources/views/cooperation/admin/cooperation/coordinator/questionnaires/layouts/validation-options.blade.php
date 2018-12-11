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
        <select class="form-control validation" name="validation[{{$question->id}}][main-rule]" id="">
            @foreach(__('woningdossier.cooperation.admin.custom-fields.index.rules') as $rule => $translation)
                <option @if($mainValidationRule == $rule) selected @endif value="{{$rule}}">{{$translation}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-sm-4">
    <div class="form-group">
        @foreach(__('woningdossier.cooperation.admin.custom-fields.index.rules') as $rule => $translation)
            <select class="form-control sub-rule" data-sub-rule="{{$rule}}" name="validation[{{$question->id}}][sub-rule]" @if($rule != $mainValidationRule) disabled="disabled" style="display: none;" @endif>
                @foreach(__('woningdossier.cooperation.admin.custom-fields.index.optional-rules.'.$rule) as $optionalRule => $optionalRuleTranslation)
                    <option @if($optionalValidationRule == $optionalRule) selected @endif value="{{$optionalRule}}">{{$optionalRuleTranslation}}</option>
                @endforeach
            </select>
        @endforeach
    </div>
</div>

@if(is_array($question->validation))
    @foreach($question->validation[$mainValidationRule] as $subRule => $subRuleCheckValues)
        @foreach($subRuleCheckValues as $subRuleCheckValue)
            <div class="col-sm-2">
                <div class="form-group">
                    <input type="text" name="validation[{{$question->id}}][sub-rule-check-value][]" class="form-control" value="{{$subRuleCheckValue}}">
                </div>
            </div>
        @endforeach
    @endforeach
@endif