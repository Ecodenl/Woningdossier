<div class="w-full">

    @foreach($toolQuestions as $toolQuestion)
        @component('cooperation.frontend.layouts.components.form-group', [
            'class' => 'form-group-heading',
            'label' => $toolQuestion->name,
        ])
            @slot('modalBodySlot')
                <p>
                    {!! $toolQuestion->help_text !!}
                </p>
            @endslot
        <?php
            $answerForInputSource = $building->getAnswer(\App\Helpers\HoomdossierSession::getInputSource(true), $toolQuestion);
            $answerForToolQuestion = old(
                $toolQuestion->save_in ?? $toolQuestion->short,
                $answerForInputSource
            );
        ?>
            <div class="w-full grid grid-rows-2 grid-cols-4 grid-flow-row justify-items-center">
                @foreach($toolQuestion->getQuestionValues() as $toolQuestionValue)
                    <div class="radio-wrapper media-wrapper">
                        <input type="radio"
                               @if($toolQuestionValue['id'] == $answerForToolQuestion)
                                   checked="checked"
                               @endif
                               id="{{$toolQuestionValue['short'] ?? $toolQuestionValue['calculate_value']}}"
                               name="heating_type"
                               value="{{$toolQuestionValue['id']}}"
                        >
                        <label for="{{$toolQuestionValue['short'] ?? $toolQuestionValue['calculate_value']}}">
                            <span class="media-icon-wrapper">
                                <i class="{{$toolQuestionValue['extra']['icon']}}"></i>
                            </span>
                            <span class="checkmark"></span>
                            <span>{{$toolQuestionValue['name']}}</span>
                        </label>
                    </div>
                @endforeach
            </div>
        @endcomponent
</div>
@endforeach
