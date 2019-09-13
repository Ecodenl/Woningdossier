@isset($commentsByStep[$stepSlug])
<div class="question-answer-section">
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.comments')}}</p>
    @foreach($commentsByStep[$stepSlug] as $inputSourceName => $commentsCategorizedUnderColumn)
        {{-- The column can be a category, this will be the case when the comment is stored under a catergory --}}
        @foreach($commentsCategorizedUnderColumn as $columnOrCategory => $comment)
            <table class="full-width">
                <tbody>
                    <tr class="h-20">
                        <td class="w-100">{{$inputSourceName}}</td>
                        <td>{{$comment}}</td>
                    </tr>
                </tbody>
            </table>
        @endforeach
    @endforeach
</div>
@endisset
