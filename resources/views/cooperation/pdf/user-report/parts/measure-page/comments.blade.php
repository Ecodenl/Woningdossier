<div class="question-answer-section">
    <p class="lead">{{$title ?? __('pdf/user-report.measure-pages.comments.title')}}</p>
    @foreach($comments as $inputSourceName => $comment)
        {{-- The column can be a category, this will be the case when the comment is stored under a catergory --}}
        <table class="full-width">
            <tbody>
                @if(is_array($comment))
                    @foreach($comment as $short => $answer)
                        @if(! empty($answer))
                            <tr class="h-20">
                                <td class="w-100">{{$inputSourceName}} (@lang("pdf/user-report.measure-pages.comments.short-translations.{$short}"))</td>
                            </tr>
                            <tr>
                                <td style="word-wrap: break-word !important;">{!! nl2br(strip_tags($answer)) !!}</td>
                            </tr>
                        @endif
                    @endforeach
                @else
                    <tr class="h-20">
                        <td class="w-100">{{$inputSourceName}}</td>
                    </tr>
                    <tr>
                        <td style="word-wrap: break-word !important;">{!! nl2br(strip_tags($comment)) !!}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endforeach
</div>
