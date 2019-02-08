@foreach($userInputValues as $userInputValue)
    @foreach ($inputValues as $inputValue)
        @if ($userInputValue->answer == $inputValue->id)
            <li class="change-input-value" data-input-source-short="{{$userInputValue->inputSource->short}}" data-input-value="{{ $inputValue->id }}"><a href="#">{{ $userInputValue->inputSource->name }}: {{ $inputValue->name }}</a></li>
        @endif
    @endforeach
@endforeach
