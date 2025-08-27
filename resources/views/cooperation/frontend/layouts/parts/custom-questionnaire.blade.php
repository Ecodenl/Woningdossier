@php $isInExpertTool = RouteLogic::inExpertTool(Route::currentRouteName()); @endphp

<div class="w-full divide-y divide-blue-500 divide-opacity-50"
     @if(($isTab ?? false)) x-cloak x-show="currentTab === $el" @endif
     id="questionnaire-{{$questionnaire->id}}">
    @if($isInExpertTool)
        <div class="py-8">
            <h3 class="heading-3 inline-block">
                {{$questionnaire->name}}
            </h3>

            {{-- TODO: ShowSave as legacy support, but perhaps ready for deprecation anyway? --}}
            @if(!\App\helpers\HoomdossierSession::isUserObserving() && ($showSave ?? false))
                <button id="submit-custom-questionnaire-{{$questionnaire->id}}"
                        data-questionnaire-id="{{$questionnaire->id}}" class="float-right btn btn-purple">
                    @lang('default.buttons.save')
                </button>
            @endif
        </div>
    @endif

    <div class="py-8">
        <form action="{{ route('cooperation.tool.questionnaire.store') }}"
              id="questionnaire-form-{{$questionnaire->id}}" method="post">
            @csrf
            <input type="hidden" name="tab_id" value="#questionnaire-{{$questionnaire->id}}">
            <input type="hidden" name="questionnaire_id" value="{{$questionnaire->id}}">
            <input type="hidden" name="step_short" value="{{$step->short}}">
            @foreach($questionnaire->questions as $question)
                @include("cooperation.tool.questionnaires.{$question->type}", ['question' => $question])
            @endforeach
            @if(! \App\helpers\HoomdossierSession::isUserObserving() && $isInExpertTool && ($showSave ?? false))
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

@if($isInExpertTool)
    @push('js')
        <script type="module" nonce="{{ $cspNonce }}">
            $('button[id*=submit-custom-questionnaire]').on('click', function () {
                var questionnaireId = $(this).data('questionnaire-id');
                // we could just find the questionnaire form and submit it, but the html5 validation wont be triggered.
                // so we find the submit button in the questionnaire form and click that one.
                $('body').find('#questionnaire-form-' + questionnaireId).find('button[type=submit]').click();
            })
        </script>
    @endpush
@endif