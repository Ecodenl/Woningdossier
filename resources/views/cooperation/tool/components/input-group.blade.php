
<div class="input-group input-source-group">
    {{ $slot }}
    <div class="input-group-btn">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
        <ul class="dropdown-menu">
            @switch($inputType)
                @case('select')
                    @foreach($inputValues as $inputValue)
                        @foreach($userInputValues as $userInputValue)
                            @if($inputValue->id == $userInputValue->$userInputColumn)
                                <li class="change-input-value" data-input-value="{{$inputValue->id}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{array_key_exists('value', $inputValue->attributesToArray()) ? $inputValue->value : $inputValue->name}}</a></li>
                            @endif
                        @endforeach
                    @endforeach
                    @break
                @case('input')
                    @foreach($userInputValues as $userInputValue)
                        <li class="change-input-value" data-input-value="{{$userInputValue->$userInputColumn}}"><a href="#">{{$userInputValue->getInputSourceName()}}: {{$userInputValue->$userInputColumn}}</a></li>
                    @endforeach
                    @break
                @default
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