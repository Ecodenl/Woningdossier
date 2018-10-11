<div class="input-group">
    {{ $slot }}
    <div class="input-group-btn">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
        <ul class="dropdown-menu">
            @switch($inputType)
                @case('select')
                    @foreach($inputValues as $inputValue)
                        @foreach($userInputValues as $userInputValue)
                            @if($inputValue->id == $userInputValue->$userInputColumn)
                                <li><a href="#">: {{$inputValue->name}}</a></li>
                            @endif
                        @endforeach
                    @endforeach
                    @break
                @case('input')
                    @foreach($userInputValues as $userInputValue)
                        <li><a href="#">{{$userInputValue->getInputSourceName()}}: {{$userInputValue->$userInputColumn}}</a></li>
                    @endforeach
                    @break
                @case('radio')
                    <li>i have no clue</li>
                    @break

                @default
            @endswitch
        </ul>
    </div>
</div>