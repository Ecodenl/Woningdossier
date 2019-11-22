@isset($commentsByStep[$stepShort][$subStepShort])
<div class="question-answer-section">
    <p class="lead">{{__('pdf/user-report.measure-pages.comments')}}</p>
    @foreach($commentsByStep[$stepShort][$subStepShort] as $inputSourceName => $comment)
        {{-- The column can be a category, this will be the case when the comment is stored under a catergory --}}
        <table class="full-width">
            <tbody>
                @if(is_array($comment))
                    @foreach($comment as $short => $comment)
                        <tr class="h-20">
                            <td class="w-100">{{$inputSourceName}} ({{$short}})</td>
                            <td>{{$comment}}</td>
                        </tr>
                    @endforeach
                @else
                <tr class="h-20">
                    <td class="w-100">{{$inputSourceName}}</td>
                    <td>{{$comment}}</td>
                </tr>
                @endif
            </tbody>
        </table>
    @endforeach
</div>
@endisset
