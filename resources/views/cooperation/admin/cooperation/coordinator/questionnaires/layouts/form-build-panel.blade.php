<div class="form-builder ui-sortable-handle panel panel-default" @isset($id) id="{{$id}}" @endisset>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12">
                {{$slot}}
            </div>
            <div class="col-sm-12 question">
                <input type="hidden" name="questions[edit][{{$question->id}}][type]" value="{{$question->type}}">
                <input type="hidden" name="questions[edit][{{$question->id}}][question_id]" class="question_id" value="{{$question->id}}">
                @switch($question->type)

                    @case('text')
                        @include('cooperation.admin.cooperation.coordinator.questionnaires.layouts.inputs.text', ['question' => $question])
                    @break
                    @case('select')
                        @include('cooperation.admin.cooperation.coordinator.questionnaires.layouts.inputs.select', ['question' => $question])
                    @break

                @endswitch


            </div>
        </div>
        <div class="row validation-inputs">
            @if(in_array($question->type, ['text']))
                @include('cooperation.admin.cooperation.coordinator.questionnaires.layouts.validation-options', ['question' => $question])
            @endif
        </div>
    </div>
    <div class="panel-footer">
        @include('cooperation.admin.cooperation.coordinator.questionnaires.layouts.form-build-panel-footer', ['question' => $question])
    </div>
</div>

