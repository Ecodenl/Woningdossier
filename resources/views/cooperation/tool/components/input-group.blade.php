
<div class="input-group input-source-group">
    {{ $slot }}
    <div class="input-group-btn">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
        <ul class="dropdown-menu">
            @switch($inputType)
                @case('select')
                    {{-- TODO: could use some improvement, do not know how atm--}}
                    @if(is_array($inputValues) && is_int(key($inputValues)))
                        @foreach($inputValues as $i => $inputValue)
                            @foreach($userInputValues as $userInputValue)
                                {{--we use array get, we cant use it like $userInputValue->$userInputColumn--}}
                                <?php
                                    // check if the input column has dots, ifso we need to use the array get function
                                    // else its a property that we can access
                                    if (strpos($userInputColumn, '.') !== false) {
                                        $compareValue = array_get($userInputValue, $userInputColumn);
                                    } else {
                                        $compareValue = $userInputValue->$userInputColumn;
                                    }
                                ?>
                                @if($i == $compareValue)
                                    <li class="change-input-value" data-input-value="{{$i}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{$inputValue}}</a></li>
                                @endif
                            @endforeach
                        @endforeach
                    @else
                        @foreach($inputValues as $inputValue)
                            @foreach($userInputValues as $userInputValue)
                                <?php
                                    if (isset($userInputModel)) {
                                        $value = $userInputValue->$userInputModel->$userInputColumn;
                                    } else {
                                        if (strpos($userInputColumn, ".") !== false) {
                                            $value = array_get($userInputValue, $userInputColumn);
                                        } else {
                                            $value = $userInputValue->$userInputColumn;
                                        }
                                    }

                                    if (isset($customInputValueColumn)) {
                                        $inputName = $inputValue->$customInputValueColumn;
                                    } else if (array_key_exists('value', $inputValue->attributesToArray())) {
                                        $inputName = $inputValue->value;
                                    } else {
                                        $inputName = $inputValue->name;
                                    }

                                ?>
                                @if($inputValue->id == $value)
                                    <li class="change-input-value" data-input-value="{{$inputValue->id}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{$inputName}}</a></li>
                                @endif
                            @endforeach
                        @endforeach
                    @endif
                    @break

                @case('input')
                    @foreach($userInputValues as $userInputValue)
                        <?php
                            // simple check if the user input column has dots, if it does it means we have to get a array from the row so we use the array_get method
                            if (strpos($userInputColumn, ".") !== false) {
                                $value = array_get($userInputValue, $userInputColumn);
                            } else {
                                $value = $userInputValue->$userInputColumn;
                            }
                        ?>
                        @if(isset($needsFormat))
                            <li class="change-input-value" data-input-value="{{$value}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{\App\Helpers\NumberFormatter::format($value, 1)}}</a></li>
                        @else
                            <li class="change-input-value" data-input-value="{{$value}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{$value}}</a></li>
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