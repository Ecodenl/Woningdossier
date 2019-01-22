<div class="input-group input-source-group">
    {{ $slot }}
    <div class="input-group-btn">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            @switch($inputType)
                @case('select')
                    @include('cooperation.tool.questionnaires.components.includes.select', [
                        'userInputValues' => $userInputValues,
                        'inputValues' => $inputValues,
                    ])
                @break
                @case('input')
                    @include('cooperation.tool.questionnaires.components.includes.input', [
                        'userInputValues' => $userInputValues,
                        'userInputColumn' => isset($userInputColumn) ? $userInputColumn : "answer"
                    ])
                    @break
                @default

                @break
                @case('checkbox')
                    @include('cooperation.tool.questionnaires.components.includes.checkbox', [
                        'userInputValues' => $userInputValues,
                        'inputValues' => $inputValues,
                    ])
                    @break
                @case('radio')
                    @include('cooperation.tool.questionnaires.components.includes.radio', [
                        'userInputValues' => $userInputValues,
                        'inputValues' => $inputValues,
                    ])
                    @break
            @endswitch
        </ul>
    </div>
</div>