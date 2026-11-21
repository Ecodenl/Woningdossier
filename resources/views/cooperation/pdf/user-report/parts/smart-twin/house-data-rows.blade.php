{{--
    Questions and answers from App\Services\SmartTwin\HouseData. A string answer comes from
    ToolQuestionHelper::getHumanReadableAnswer(), which already escapes what someone typed in.
--}}
@foreach($rows as $row)
    @include('cooperation.pdf.user-report.parts.answer', [
        'label' => $row['label'],
        'answer' => is_array($row['answer'])
            ? collect($row['answer'])->map(fn ($answer, $name) => e("{$name}: {$answer}"))->implode('<br>')
            : $row['answer'],
    ])
@endforeach
