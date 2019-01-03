<?php
/**
 * @var $infoAlertId
 * Id to trigger a collapsable alert with info about a given section
 * @var $translationKey
 * The key for the uuid.php translation file WITHOUT the .title or .help on the ending, we concat this later on.
 */
?>

<h4 style="margin-left: -5px;">
    @isset($infoAlertId)
        <i data-toggle="collapse" data-target="#{{$infoAlertId}}" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
    @endisset
    {{\App\Helpers\Translation::translate($translationKey.'.title')}}
</h4>
@isset($infoAlertId)
    @component('cooperation.tool.components.alert', ['alertType' => 'info', 'id' => $infoAlertId, 'collapsable' => true])
        {{\App\Helpers\Translation::translate($translationKey.'.help')}}
    @endcomponent
@endisset