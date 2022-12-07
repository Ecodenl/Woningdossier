<div class="question-answer-section">
    @foreach ($dataForStep as $label => $fields)
        @if(! empty($label))
            <p class="lead">
                {{ $label }}
            </p>
        @endif
        @if(! empty($fields))
            <table class="full-width" style="margin-bottom: 1rem">
                <tbody>
                    @foreach($fields as $key => $fieldData)
                        <tr class="h-20">
                            <td class="w-380">{{$fieldData['label']}}</td>
                            <td>{{$fieldData['value']}} @if(! empty($fieldData['unit'])) {!!  $fieldData['unit'] !!} @endif</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach
</div>