{{--
    Questions and answers as the dossier's summaries show them. A string answer comes from
    ToolQuestionHelper::getHumanReadableAnswer(), which already escapes what someone typed in.
--}}
<dl class="w-full">
    @foreach($rows as $row)
        <div class="flex flex-row flex-wrap w-full py-1 border-b border-solid border-blue-500 border-opacity-10">
            <dt class="w-1/2 pr-2">{{ $row['label'] }}</dt>
            <dd class="w-1/2 font-bold">
                @if(is_array($row['answer']))
                    @foreach($row['answer'] as $name => $answer)
                        <span class="block">{{ $name }}: {{ $answer }}</span>
                    @endforeach
                @else
                    {!! $row['answer'] !!}
                @endif
            </dd>
        </div>
    @endforeach
</dl>
