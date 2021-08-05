@if(isset($currentStep) && $currentStep->hasQuestionnaires())
    @foreach($currentStep->questionnaires as $questionnaire)
        @if($questionnaire->isActive())
            <div x-cloak class="w-full divide-y divide-blue-500 divide-opacity-50"
                 id="questionnaire-{{$questionnaire->id}}" x-show="currentTab == $el">
                <div class="px-4 py-8">
                    <h3 class="heading-3 inline-block">
                        {{$questionnaire->name}}
                    </h3>

                    @if(!\App\helpers\HoomdossierSession::isUserObserving())
                        <button id="submit-custom-questionnaire-{{$questionnaire->id}}"
                                data-questionnaire-id="{{$questionnaire->id}}" class="float-right btn btn-purple">
                            @lang('default.buttons.next')
                        </button>
                    @endif
                </div>

                <div class="px-4 py-8">
                    <form action="{{route('cooperation.tool.questionnaire.store')}}"
                          id="questionnaire-form-{{$questionnaire->id}}" method="post">
                        @csrf
                        <input type="hidden" name="tab_id" value="#questionnaire-{{$questionnaire->id}}">
                        <input type="hidden" name="questionnaire_id" value="{{$questionnaire->id}}">
                        @foreach($questionnaire->questions as $question)
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
                        @if(!\App\helpers\HoomdossierSession::isUserObserving())
                            <div class="flex flex-row flex-wrap w-full">
                                <div class="w-full">
                                    <div class="my-4 px-2">
                                        <button type="submit" class="float-right btn btn-purple">
                                            @lang('default.buttons.save')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
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
            // we could just find the questionnaire form and submit it, but the html5 validation wont be triggered.
            // so we find the submit button in the questionnaire form and click that one.
            $('body').find('#questionnaire-form-'+questionnaireId).find('button[type=submit]').click();
        })
    </script>e
@endpush
