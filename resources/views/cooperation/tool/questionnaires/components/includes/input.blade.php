@foreach($userInputValues as $userInputValue)
    <li class="change-input-value" data-input-source-short="{{$userInputValue->inputSource->short}}" data-input-value="{{ $userInputValue->answer }}">
        {{ $userInputValue->inputSource->name }}: {{ $userInputValue->answer }}
    </li>
@endforeach
