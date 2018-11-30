<div class="col-sm-4">
    <div class="form-group">
        <select class="form-control" name="validation[{{$id}}]" id="">
            @foreach(__('woningdossier.cooperation.admin.custom-fields.index.rules') as $rule => $translation)
                <option value="{{$rule}}">{{$translation}}</option>@endforeach
        </select>
    </div>
</div>
<div class="col-sm-4">
    <div class="form-group">
        @foreach(__('woningdossier.cooperation.admin.custom-fields.index.rules') as $rule => $translation)
            <select class="form-control" name="validation-options[{{$id}}]" id="{{$rule}}">
                @foreach(__('woningdossier.cooperation.admin.custom-fields.index.optional-rules.'.$rule) as $optionalRule => $optionalRuleTranslation)
                    <option value="{{$optionalRule}}">{{$optionalRuleTranslation}}</option>
                @endforeach
            </select>
        @endforeach
    </div>
</div>