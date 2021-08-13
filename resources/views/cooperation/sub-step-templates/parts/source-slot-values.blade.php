@foreach($values as $inputSourceShort => $answer)
    @php
        $humanReadableAnswer = null;

        $json = json_decode($answer, true);
        if (is_null($json)) {
            $humanReadableAnswer = $answer;
        } else {
            $formatted = [];

            // We try to build the input source names based off the options, but if they aren't available,
            // we will use the shorts provided
            if (! empty($toolQuestion->options)) {
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

    <li class="change-input-value" data-input-source-short="{{$inputSourceShort}}" data-input-value="{{$answer}}">
        {{\App\Models\InputSource::findByShort($inputSourceShort)->name}}: {{$humanReadableAnswer}}
    </li>
@endforeach