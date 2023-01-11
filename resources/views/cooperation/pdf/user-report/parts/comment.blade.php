{{--<div class="row">--}}
{{--    <div class="col-12">--}}
{{--        <p>--}}
{{--            {{ $comment->inputSource->name }}:--}}
{{--        </p>--}}
{{--    </div>--}}
{{--</div>--}}
{{--<div class="row">--}}
{{--    <div class="col-12">--}}
{{--        <p class="comment">--}}
{{--            {{ trim($comment->comment) }}--}}
{{--        </p>--}}
{{--    </div>--}}
{{--</div>--}}

<table>
    <tbody>
        <tr>
            <td>
                {{$comment->inputSource->name}}:
            </td>
        </tr>
        <tr>
            <td class="comment">
                {{ trim($comment->comment) }}
            </td>
        </tr>
    </tbody>
</table>