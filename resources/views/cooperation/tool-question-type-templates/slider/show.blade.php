@include('cooperation.frontend.layouts.parts.slider', [
    'min' => $toolQuestion->options['min'],
    'max' => $toolQuestion->options['max'],
    'step' => $toolQuestion->options['step'],
    'unit' => $toolQuestion->unit_of_measure,
])