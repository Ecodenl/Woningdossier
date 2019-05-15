<div class="input-group input-source-group">
    {{ $slot }}
    <div class="input-group-btn">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <?php
                /**
                 * @var \Illuminate\Support\Collection $inputValues
                 * @var \Illuminate\Support\Collection $userInputValues
                 */

                $hasAnswer = false;

                // if there are multiple, it means a other input source did some work.
                // so in that case there are answers and complicated checks are not needed
                if ($userInputValues->count() > 1) {
                    $hasAnswer = true;
                } else {
                    // check if there are actualy answers
                    if ($userInputValues instanceof \Illuminate\Support\Collection && $userInputValues->isNotEmpty()) {
                        // check what we need to do.
                        if (in_array($inputType, ['select', 'radio', 'checkbox'])) {
                            // count all the possible answers
                            $possibleAnswerCount = $inputValues->count();

                            $userInputValueAnswers = explode('|', $userInputValues->first()->answer);
                            $possibleAnswers       = $inputValues->pluck('id')->toArray();

                            // get the difference between the user answers and all the possible answers.
                            $differenceBetweenPossibleAndAnswers = array_diff($possibleAnswers, $userInputValueAnswers);

                            // if the count is not the same, it means there is a difference. There can only be a difference if a user gave an answer.
                            if ($possibleAnswerCount != count($differenceBetweenPossibleAndAnswers)) {
                                $hasAnswer = true;
                            }
                        } else {
                            if ($userInputValues->contains($userInputColumn ?? 'answer', '!=', '')) {
                                $hasAnswer = true;
                            }
                        }
                    }
                }

            ?>
            @if(!$hasAnswer)
                @include('cooperation.tool.includes.no-answer-available')
            @else
                @switch($inputType)
                    @case('select')
                        @include('cooperation.tool.questionnaires.components.includes.select', [
                            'userInputValues' => $userInputValues,
                            'inputValues' => $inputValues,
                        ])
                        @break
                    @case('input')
                        @include('cooperation.tool.questionnaires.components.includes.input', [
                            'userInputValues' => $userInputValues,
                            'userInputColumn' => $userInputColumn ?? 'answer'
                        ])
                        @break
                    @default

                    @break
                    @case('checkbox')
                        @include('cooperation.tool.questionnaires.components.includes.checkbox', [
                            'userInputValues' => $userInputValues,
                            'inputValues' => $inputValues,
                        ])
                        @break
                    @case('radio')
                        @include('cooperation.tool.questionnaires.components.includes.radio', [
                            'userInputValues' => $userInputValues,
                            'inputValues' => $inputValues,
                        ])
                        @break
                @endswitch
            @endif
        </ul>
    </div>
</div>