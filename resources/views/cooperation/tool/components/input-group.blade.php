<div class="input-group input-source-group">
    {{ $slot }}
    <div class="input-group-btn">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
        <ul class="dropdown-menu">
            <?php
                // we need to check if there is a answer from one input source
                if($userInputValues instanceof \Illuminate\Support\Collection) {
                    $hasAnswer = $userInputValues->contains($userInputColumn, '!=', '');
                } else {
                    $hasAnswer = collect($userInputValues)->contains($userInputColumn, '!=', '');
                }
            ?>
            @if(!$hasAnswer)
                @include('cooperation.tool.includes.no-answer-available')
            @else
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
                            'needsFormat' => isset($needsFormat) ? true : false,
                            'decimals' => $decimals ?? null,
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
            @endif
        </ul>
    </div>
</div>