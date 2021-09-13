<?php
    /* usage:
    @component('cooperation.tool.components.step-question',[
        'id' => 'building_type_id',
        'name' => .. optional (defaults to same as id),
        'translation' => translation key without last '.title' or '.help'
        'required' => true | false

   ])

    @endcomponent
    */

    // set some defaults if not given
    $name = $name ?? $id;
    $required = $required ?? false;
    $withInputSource = $withInputSource ?? true;

    $translationReplace = $translationReplace ?? [];

    // we show the help icon when there is a text available and its not empty
    $hasHelpTranslation = isset($translation) ? \App\Helpers\Translation::hasTranslation($translation . '.help') : false;
    $shouldShowHelpIcon =  $hasHelpTranslation && !empty(__($translation . '.help'));

    $label = (isset($translation) ? __($translation . '.title', $translationReplace) : '') . ' ' . ($label ?? '');
?>

@component('cooperation.frontend.layouts.components.form-group', [
    'inputName' => $name,
    'label' => $label,
    'id' => $id,
    'modalId' => $id . '-info',
    'class' => ($required ? 'required' : '') . ' ' . ($class ?? ''),
    'inputGroupClass' => $inputGroupClass ?? '',
    'withInputSource' => $withInputSource,
])
    @if(! empty($sourceSlot))
        @slot('sourceSlot')
            {!! $sourceSlot !!}
        @endslot
    @endif

    @if(isset($translation) && $shouldShowHelpIcon)
        @slot('modalBodySlot')
            {!! __($translation . '.help', $translationReplace) !!}
        @endslot
    @endif

    {{ $slot }}
@endcomponent