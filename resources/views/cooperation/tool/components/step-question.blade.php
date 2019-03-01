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

?>

<div class="form-group add-space{{ $errors->has($id) ? ' has-error' : '' }}">
    <label for="{{ $id }}" class=" control-label">
        <?php // show help icon? ?>
        @if(\App\Helpers\Translation::hasTranslation($translation . '.help'))
            <i data-target="#{{ $id }}-info"
               class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
               aria-expanded="false"></i>
        @endif
        {{\App\Helpers\Translation::translate($translation . '.title')}}
        @if($required)
            <span>*</span>
        @endif
    </label>

    {{ $slot }}

	<?php // show include modal for help icon? ?>
    @if(\App\Helpers\Translation::hasTranslation($translation . '.help'))
    @component('cooperation.tool.components.help-modal', ['id' => $id . "-info"])
        {{\App\Helpers\Translation::translate($translation . '.help')}}
    @endcomponent
    @endif

	<?php // show error? ?>
    @if ($errors->has($name))
        <span class="help-block">
            <strong>{{ $errors->first($name) }}</strong>
        </span>
    @endif
</div>
