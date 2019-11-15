@isset($commentsByStep[$stepShort][$subStepShort])
<div class="question-answer-section">
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.comments')}}</p>
    @foreach($commentsByStep[$stepShort][$subStepShort] as $inputSourceName => $comment)
        {{-- The column can be a category, this will be the case when the comment is stored under a catergory --}}
        <table class="full-width">
            <tbody>
                <tr class="h-20">
                    <td class="w-100">{{$inputSourceName}}</td>
                    <td>{{$comment}}</td>
                </tr>
            </tbody>
        </table>
    @endforeach
</div>
@endisset
