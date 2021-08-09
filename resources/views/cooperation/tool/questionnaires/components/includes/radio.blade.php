@foreach($userInputValues as $userInputValue)
    @foreach ($inputValues as $inputValue)
        @if ($userInputValue->answer == $inputValue->id)
            <li class="change-input-value" data-input-source-short="{{$userInputValue->inputSource->short}}" data-input-value="{{ $inputValue->id }}">
                {{ $userInputValue->inputSource->name }}: {{ $inputValue->name }}
            </li>
        @endif
    @endforeach
@endforeach
