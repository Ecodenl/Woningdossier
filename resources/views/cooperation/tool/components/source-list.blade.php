<?php
// we need to check if there is a answer from one input source
$masterInputSource = \App\Models\InputSource::findByShort(\App\Models\InputSource::MASTER_SHORT);
if($userInputValues instanceof \Illuminate\Support\Collection) {
    $hasAnswer = $userInputValues->contains($userInputColumn, '!=', '');

    // remove the master input source his answer from the collection of models
    $userInputValues = $userInputValues->reject(fn($model) => $model->input_source_id == $masterInputSource->id);
} else {
    $userInputValues = collect($userInputValues)->reject(fn($model) => $model->input_source_id == $masterInputSource->id);
    $hasAnswer = $userInputValues->contains($userInputColumn, '!=', '');
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