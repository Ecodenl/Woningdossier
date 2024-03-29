<?php
/**
 * @var
 * Id to trigger a collapsable alert with info about a given section
 * @var $translationKey
 *                      The key for the uuid.php translation file WITHOUT the .title or .help on the ending, we concat this later on.
 */

// we show the help icon when there is a text available and its not empty
$hasHelpTranslation = isset($translation) ? \App\Helpers\Translation::hasTranslation($translation . '.help') : false;
$shouldShowHelpIcon =  $hasHelpTranslation && !empty(__($translation . '.help'));
?>

<div class="section-title" x-data="modal()">
    <h4 class="heading-4" style="margin-left: -5px;">
        @if(\App\Helpers\Translation::hasTranslation($translation.'.help') && $shouldShowHelpIcon)
            <i data-target="#{{$id}}-info"
               class="icon-sm icon-info-light clickable" x-on:click="toggle()"></i>
        @endif
        {{\App\Helpers\Translation::translate($translation.'.title')}}
    </h4>
    @if(\App\Helpers\Translation::hasTranslation($translation.'.help') && $shouldShowHelpIcon)
        @component('cooperation.frontend.layouts.components.modal', ['id' => $id])
            {!! \App\Helpers\Translation::translate($translation.'.help') !!}
        @endcomponent
    @endif
</div>