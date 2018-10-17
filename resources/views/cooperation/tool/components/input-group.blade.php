
<div class="input-group input-source-group">
    {{ $slot }}
    <div class="input-group-btn">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
        <ul class="dropdown-menu">
            @switch($inputType)
                @case('select')
                    @if(is_array($inputValues))
                        @foreach($inputValues as $i => $inputValue)
                            @foreach($userInputValues as $userInputValue)
                                {{--we use array get, we cant use it like $userInputValue->$userInputColumn--}}
                                @if($i == array_get($userInputValue, $userInputColumn))
                                    <li class="change-input-value" data-input-value="{{$i}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{$inputValue}}</a></li>
                                @endif
                            @endforeach
                        @endforeach
                    @else
                        @foreach($inputValues as $inputValue)
                            @foreach($userInputValues as $userInputValue)
                                @if($inputValue->id == $userInputValue->$userInputColumn)
                                    <li class="change-input-value" data-input-value="{{$inputValue->id}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{array_key_exists('value', $inputValue->attributesToArray()) ? $inputValue->value : $inputValue->name}}</a></li>
                                @endif
                            @endforeach
                        @endforeach
                    @endif
                    @break
                @case('select-extended')

                @case('input')
                    @foreach($userInputValues as $userInputValue)
                        @if(isset($needsFormat))
                            <li class="change-input-value" data-input-value="{{$userInputValue->$userInputColumn}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{\App\Helpers\NumberFormatter::format($userInputValue->$userInputColumn, 1)}}</a></li>
                        @else
                            <li class="change-input-value" data-input-value="{{$userInputValue->$userInputColumn}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{$userInputValue->$userInputColumn}}</a></li>
                        @endif
                    @endforeach
                    @break
                @default

                {{--
                    TODO: Create a way so this can always be used, currently this is only used on the roof insulation.
                --}}
                @case('checkbox')
                    @foreach($inputValues as $inputValue)
                        @foreach($userInputValues as $userInputValue)
                            @if($inputValue->id == $userInputValue->$userInputColumn)
                                <li class="change-input-value" data-input-value="{{$inputValue->id}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{array_key_exists('value', $inputValue->attributesToArray()) ? $inputValue->value : $inputValue->name}}</a></li>
                            @endif
                        @endforeach
                    @endforeach

                @case('radio')
                    @if(is_array($inputValues))
                        @foreach($inputValues as $inputValue)
                            @foreach($userInputValues as $userInputValue)
                                @if($inputValue == $userInputValue->$userInputColumn)
                                    <li class="change-input-value" data-input-value="{{$userInputValue->$userInputColumn}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{$userInputValue->$userInputColumn}}</a></li>
                                @endif
                            @endforeach
                        @endforeach
                    @endif

            @endswitch
        </ul>
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function () {

            //TODO: could use some tweaks, event fires 16 times more as needed ?
            $('.input-source-group').on('click', 'li.change-input-value', function (event) {

                // so it will not jump to the top of the page.
                event.preventDefault();

                var dataInputValue = $(this).data('input-value');

                // find the selected option
                var inputSourceGroup = $(this).parent().parent().parent();
                var inputType = inputSourceGroup.find('input').attr('type');

                // check if the input is a "input" and not a select
                if (typeof inputType !== 'undefined') {

                    switch (inputType) {
                         case "text":
                            inputSourceGroup.find('input[type=text]').val(dataInputValue);
                            break;
                        case "radio":
                            inputSourceGroup.find('input[type=radio]:checked').removeAttr('selected');
                            inputSourceGroup.find('input[value='+dataInputValue+']').attr('selected', true);
                            break;
                        case "checkbox":
                            inputSourceGroup.find('input[type=checkbox]:checked').removeAttr('selected');
                            inputSourceGroup.find('input[value='+dataInputValue+']').attr('selected', true);
                            break;
                        default:
                            console.log('Something went tremendously wrong...');
                            '{{Log::alert('Something went tremendously wrong...')}}';
                            break;
                    }
                    // its a select.
                } else {
                    inputSourceGroup.find('select option:selected').removeAttr('selected');
                    inputSourceGroup.find('select option[value='+dataInputValue+']').attr('selected', true);
                }

            });


        })
    </script>
@endpush