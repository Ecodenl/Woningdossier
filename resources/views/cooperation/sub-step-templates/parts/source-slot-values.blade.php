@if(! empty($values))
    @foreach($values as $inputSourceShort => $answersForInputSources)
        @foreach($answersForInputSources as $answerForInputSource)
            @php
                $value = $answerForInputSource['value'];
                $answer = $answerForInputSource['answer'];
                $humanReadableAnswer = null;

                if (! \App\Helpers\Str::isValidJson($answer)) {
                    if ($toolQuestion->data_type === \App\Helpers\DataTypes\Caster::INT) {
                        $value = \App\Helpers\NumberFormatter::format($value, 0);
                        $humanReadableAnswer = \App\Helpers\NumberFormatter::format($answer, 0);
                    } elseif ($toolQuestion->data_type === \App\Helpers\DataTypes\Caster::FLOAT) {
                        $value = \App\Helpers\NumberFormatter::format($value, 1);
                        $humanReadableAnswer = \App\Helpers\NumberFormatter::format($answer, 1);
                    } else {
                        $humanReadableAnswer = $answer;
                    }
                } else {
                    $json = json_decode($answer, true);
                    $formatted = [];

                    // Value is null, we want to use the answer and prepare the JSON
                    if (is_null($value)) {
                        $value = \App\Helpers\Str::prepareJsonForHtml($answer);
                    }

                    // We try to build the input source names based off the options, but if they aren't available,
                    // we will use the shorts provided
                    // TODO: Check how often a check is done for rating-slider, perhaps change this to a function to
                    // check if a tool question has "sub questions"
                    if ($toolQuestion->data_type === \App\Helpers\DataTypes\Caster::JSON) {
                        foreach ($toolQuestion->options as $option) {
                            $formatted[$option['short']] = "{$option['name']}: {$json[$option['short']]}";
                        }
                        // Follow order of input source
                        $formatted = array_merge($json, $formatted);
                    } else {
                        foreach ($json as $short => $value) {
                            $formatted[$short] = "${short}: {$value}";
                        }
                    }

                    $humanReadableAnswer = implode(', ', $formatted);
                }
            @endphp

            <li class="change-input-value" data-input-source-short="{{$inputSourceShort}}"
                data-input-value="{{$value}}">
                {{\App\Models\InputSource::findByShort($inputSourceShort)->name}}: {{$humanReadableAnswer}}
            </li>
        @endforeach
    @endforeach
@endif