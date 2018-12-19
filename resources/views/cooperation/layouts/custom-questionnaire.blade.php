@if(isset($currentStep) && $currentStep->hasQuestionnaires())
    @foreach($currentStep->questionnaires as $questionnaire)
        @if($questionnaire->isActive())
            <div class="panel tab-pane panel-default" id="questionnaire-{{$questionnaire->id}}">
                <div class="panel-heading">
                    <h3>
                        {{$questionnaire->name}}
                    </h3>

                    <button id="submit-custom-questionnaire-{{$questionnaire->id}}" data-questionnaire-id="{{$questionnaire->id}}" class="pull-right btn btn-primary">
                        @lang('default.buttons.next')
                    </button>
                    <div class="clearfix"></div>
                </div>

                <div class="panel-body">
                    <form class="form-horizontal" action="{{route('cooperation.tool.questionnaire.store')}}"  id="questionnaire-form-{{$questionnaire->id}}" method="post">
                        {{csrf_field()}}
                        <input type="hidden" name="tab_id" value="#questionnaire-{{$questionnaire->id}}">
                        <input type="hidden" name="questionnaire_id" value="{{$questionnaire->id}}">
                        @foreach($questionnaire->questions()->orderBy('order')->get() as $question)
                            @switch($question->type)
                                @case('text')
                                    @include('cooperation.tool.questionnaires.text', ['question' => $question])
                                    @break
                                @case('textarea')
                                    @include('cooperation.tool.questionnaires.textarea', ['question' => $question])
                                    @break
                                @case('select')
                                    @include('cooperation.tool.questionnaires.select', ['question' => $question])
                                    @break
                                @case('radio')
                                    @include('cooperation.tool.questionnaires.radio', ['question' => $question])
                                    @break
                                @case('checkbox')
                                    @include('cooperation.tool.questionnaires.checkbox', ['question' => $question])
                                    @break
                                @case('date')
                                    @include('cooperation.tool.questionnaires.date', ['question' => $question])
                                    @break
                            @endswitch
                        @endforeach
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                                <div class="form-group add-space">
                                    <div class="">
                                        <button type="submit" class="pull-right btn btn-primary">
                                            Opslaan                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endforeach
@endif

@push('js')
    <script>
        $('button[id*=submit-custom-questionnaire]').on('click', function () {
            var questionnaireId = $(this).data('questionnaire-id');
            // we could just find the questionnaire form and submit it, but the html5 validation wont be triggerd.
            // so we find the submit button in the questionnaire form and click that one.
            $('body').find('#questionnaire-form-'+questionnaireId).find('button[type=submit]').click();
        })
    </script>
@endpush
