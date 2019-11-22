@isset($commentsByStep[$stepShort][$subStepShort])
<div class="question-answer-section">
    <p class="lead">@lang('pdf/user-report.measure-pages.comments.title')</p>
    @foreach($commentsByStep[$stepShort][$subStepShort] as $inputSourceName => $comment)
        {{-- The column can be a category, this will be the case when the comment is stored under a catergory --}}
        <table class="full-width">
            <tbody>
                @if(is_array($comment))
                    @foreach($comment as $short => $comment)
                        <tr class="h-20">
                            <td class="w-100">{{$inputSourceName}} (@lang("pdf/user-report.measure-pages.comments.short-translations.{$short}"))</td>
                            <td>{!!  nl2br($comment)!!}</td>
                        </tr>
                    @endforeach
                @else
                <tr class="h-20">
                    <td class="w-100">{{$inputSourceName}}</td>
                    <td>{!!  nl2br($comment)!!}</td>
                </tr>
                @endif
            </tbody>
        </table>
    @endforeach
</div>
@endisset
