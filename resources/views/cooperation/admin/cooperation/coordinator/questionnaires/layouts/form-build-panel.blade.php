<div class="form-builder ui-sortable-handle panel panel-default" @isset($id) id="{{$id}}" @endisset>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12">
                {{$slot}}
            </div>
            <div class="col-sm-12">
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
        <div class="row" id="validation-rules">
            @include('cooperation.admin.cooperation.coordinator.questionnaires.layouts.validation-options', ['question' => $question])
        </div>
    </div>
    <div class="panel-footer">
        @include('cooperation.admin.cooperation.coordinator.questionnaires.layouts.form-build-panel-footer', ['question' => $question])
    </div>
</div>

