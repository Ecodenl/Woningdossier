<?php
/**
 * @var
 * Id to trigger a collapsable alert with info about a given section
 * @var $translationKey
 *                      The key for the uuid.php translation file WITHOUT the .title or .help on the ending, we concat this later on.
 */
?>

<div class="section-title">
    <h4 style="margin-left: -5px;">
        @if(\App\Helpers\Translation::hasTranslation($translation.'.help'))
            <i data-target="#{{$id}}-info"
               class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
        @endif
        {{\App\Helpers\Translation::translate($translation.'.title')}}
    </h4>
    @if(\App\Helpers\Translation::hasTranslation($translation.'.help'))
        @component('cooperation.tool.components.help-modal', ['id' => $id])
            {{\App\Helpers\Translation::translate($translation.'.help')}}
        @endcomponent
    @endif
</div>
