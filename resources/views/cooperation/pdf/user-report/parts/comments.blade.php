@isset($commentsByStep[$stepSlug])
<div class="question-answer-section">
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.comments')}}</p>
    @foreach($commentsByStep[$stepSlug] as $inputSourceName => $commentsCategorizedUnderColumn)
        {{-- The column can be a category, this will be the case when the comment is stored under a catergory --}}
        @foreach($commentsCategorizedUnderColumn as $columnOrCategory => $comment)
            <table class="full-width">
                <tbody>
                    @if(is_array($comment))
                        @foreach($comment as $column => $c)
                            <tr class="h-20">
                                <td class="w-150">{{$inputSourceName}} ({{$columnOrCategory}})</td>
                                <td>{{$c}}</td>
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
    @endforeach
</div>
@endisset
