<div class="row">
    <div class="col-sm-12">
        <div class="pull-left">
            <a href=""><i class="glyphicon glyphicon-trash remove-question"></i></a>
        </div>
        <div class="pull-right">
            <label class="control-label" for="required-{{$question->id}}">
                @lang('default.required')
                <input id="required-{{$question->id}}" name="questions[{{$question->id}}][required]"
                       @if($question->isRequired()) checked @endif type="checkbox">
            </label>
        </div>
    </div>
</div>