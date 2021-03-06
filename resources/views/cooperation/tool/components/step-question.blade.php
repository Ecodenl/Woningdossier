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

    $labelClass = $labelClass ?? '';
    $labelStyling = $labelStyling ?? '';

    $translationReplace = $translationReplace ?? [];

    // we show the help icon when there is a text available and its not empty
    $hasHelpTranslation = isset($translation) ? \App\Helpers\Translation::hasTranslation($translation . '.help') : false;
    $shouldShowHelpIcon =  $hasHelpTranslation && !empty(__($translation . '.help'));
?>

{{-- this had the add-space class --}}
<div class="form-group {{ $errors->has($name) ? ' has-error' : '' }}" style="{{$labelStyling}}">
    {{--This caused the dropdown ?? input to trigger, on ios safari this would result in a opened select.--}}
    <label {{--for="{{ $id }}"--}} class="control-label {{$labelClass}}" style="display: inline; ">
        <?php // show help icon? ?>
        @if(isset($translation) && $shouldShowHelpIcon)
            <i data-target="#{{ $id }}-info"
               class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
               aria-expanded="false"></i>
        @endif
        {{isset($translation) ? \App\Helpers\Translation::translate($translation . '.title', $translationReplace) : ''}}
        @if($required)
            <span>*</span>
        @endif
    </label>

    {{ $slot }}

    <?php // show include modal for help icon? ?>
    @if(isset($translation) && $shouldShowHelpIcon)
        @component('cooperation.tool.components.help-modal', ['id' => $id . "-info"])
            {!!  \App\Helpers\Translation::translate($translation . '.help', $translationReplace) !!}
        @endcomponent
    @endif

	<?php // show error? ?>
    @if ($errors->has($name))
        <span class="help-block">
            <strong>{{ $errors->first($name) }}</strong>
        </span>
    @endif
</div>
