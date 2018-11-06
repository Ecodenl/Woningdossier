<div class="input-group input-source-group">
    {{ $slot }}
    <div class="input-group-btn">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
        <ul class="dropdown-menu">
            @switch($inputType)
                @case('select')
                    @include('cooperation.tool.components.select', [
                        'customInputValueColumn' => isset($customInputValueColumn) ? $customInputValueColumn : null,
                        'userInputValues' => $userInputValues,
                        'userInputColumn' => $userInputColumn,
                        'userInputModel' => isset($userInputModel) ? $userInputModel : null,
                        'inputValues' => $inputValues,
                    ])
                    @break
                @case('input')
                    @include('cooperation.tool.components.input', [
                        'userInputValues' => $userInputValues,
                        'userInputColumn' => $userInputColumn,
                        'needsFormat' => isset($needsFormat) ? true : false
                    ])
                    @break
                @default

                {{--
                    TODO: Create a way so this can always be used, currently this is only used on the roof insulation.
                --}}
                @case('checkbox')
                    @include('cooperation.tool.components.checkbox', [
                         'userInputValues' => $userInputValues,
                         'userInputColumn' => $userInputColumn,
                         'inputValues' => $inputValues,
                    ])
                    @break
                @case('radio')
                    @include('cooperation.tool.components.radio', [
                      'userInputValues' => $userInputValues,
                      'userInputColumn' => $userInputColumn,
                      'inputValues' => $inputValues,
                    ])
                    @break
            @endswitch
        </ul>
    </div>
</div>

{{--
@push('js')
    <script>
        $(document).ready(function () {

            // moved to app.js
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
                            break;
                    }
                    // its a select.
                } else {
                    inputSourceGroup.find('select option:selected').removeAttr('selected');
                    inputSourceGroup.find('select option[value='+dataInputValue+']').attr('selected', true);
                }

            });


        });
    </script>
@endpush
--}}