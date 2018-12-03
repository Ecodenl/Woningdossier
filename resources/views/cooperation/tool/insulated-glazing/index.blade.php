@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.insulated-glazing.title'))


@section('step_content')

    <?php
        $titles = [
            7 => 'glass-in-lead',
            8 => 'place-hr-only-glass',
            9 => 'place-hr-with-frame',
        ];
    ?>
    <form class="form-horizontal" method="POST" action="{{ route('cooperation.tool.insulated-glazing.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        @include('cooperation.tool.includes.interested', ['type' => 'element'])
        <div id="main-glass-questions">
            @foreach($measureApplications as $i => $measureApplication)
                @if($i > 0 && array_key_exists($measureApplication->id, $titles))
                    <hr>
                @endif
                <?php
                    if(array_key_exists($measureApplication->id, $buildingInsulatedGlazingsForMe)) {
                        $currentMeasureBuildingInsulatedGlazingForMe = $buildingInsulatedGlazingsForMe[$measureApplication->id];
                    } else {
                        $currentMeasureBuildingInsulatedGlazingForMe = [];
                    }
                ?>

                <div class="row">
                    <div class="col-sm-12">
                        @if(array_key_exists($measureApplication->id, $titles))
                            <h4>{{\App\Helpers\Translation::translate('insulated-glazing.subtitles.'.$titles[$measureApplication->id].'.title')}}</h4>
                        @endif
                        <div class="form-group add-space {{$errors->has('user_interests.'.$measureApplication->id) ? 'has-error' : '' }}">
                            <label class=" control-label">
                                <i data-toggle="collapse" data-target="#user_interests_{{ $measureApplication->id }}-info"
                                   class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                {{\App\Helpers\Translation::translate('insulated-glazing.interested-in.title.title', ['measure' => lcfirst($measureApplication->measure_name)]) }}
                            </label>

                            <select id="{{ $measureApplication->id }}" class="user-interest form-control" name="user_interests[{{ $measureApplication->id }}]" >
                                @foreach($interests as $interest)
                                    {{-- calculate_value 4 is the default --}}
                                    <option
                                            @if($interest->id == old('user_interests.' . $measureApplication->id) || (array_key_exists($measureApplication->id, $userInterests) && $interest->id == $userInterests[$measureApplication->id]))
                                            selected="selected"
                                            @elseif(Auth::user()->getInterestedType('measure_application', $measureApplication->id) != null && Auth::user()->getInterestedType('measure_application', $measureApplication->id)->interest_id == $interest->id)
                                            selected="selected"
                                            @elseif(!array_key_exists($measureApplication->id, $userInterests) && $interest->calculate_value == 4)
                                            selected="selected"
                                            @endif
                                            value="{{ $interest->id }}">{{ $interest->name }}
                                    </option>
                                @endforeach
                            </select>

                            <div id="user_interests_{{ $measureApplication->id }}-info"
                                 class="collapse alert alert-info remove-collapse-space alert-top-space">
                                {{\App\Helpers\Translation::translate('insulated-glazing.interested-in.title.help')}}
                            </div>

                            @if ($errors->has('user_interests.' . $measureApplication->id))
                                <span class="help-block">
                                    <strong>{{ $errors->first('user_interests.' . $measureApplication->id) }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="values">
                        <div class=" col-sm-3 ">
                            <div class="form-group add-space {{ $errors->has('building_insulated_glazings.' . $measureApplication->id . '.insulated_glazing_id') ? ' has-error' : '' }}">
                                <label class=" control-label">
                                    <i data-toggle="collapse" data-target="#building_insulated_glazings_{{ $measureApplication->id }}-insulating_glazing_id-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                    {{\App\Helpers\Translation::translate('insulated-glazing.current-glass.title')}}
                                </label>
                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'select', 'inputValues' => $insulatedGlazings, 'userInputValues' => $currentMeasureBuildingInsulatedGlazingForMe ,'userInputColumn' => 'insulating_glazing_id'])
                                    <select class="form-control" name="building_insulated_glazings[{{ $measureApplication->id }}][insulated_glazing_id]">
                                        @foreach($insulatedGlazings as $insulateGlazing)
                                            <option @if($insulateGlazing->id == old('building_insulated_glazings.' . $measureApplication->id . '.insulated_glazing_id') || (array_key_exists($measureApplication->id, $buildingInsulatedGlazings) && $buildingInsulatedGlazings[$measureApplication->id]->insulating_glazing_id == $insulateGlazing->id)) selected @endif value="{{ $insulateGlazing->id }}">{{ $insulateGlazing->name }}</option>
                                        @endforeach
                                    </select>
                                @endcomponent

                                <div id="building_insulated_glazings_{{ $measureApplication->id }}-insulating_glazing_id-info"
                                     class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    {{\App\Helpers\Translation::translate('insulated-glazing.current-glass.help')}} }}
                                </div>

                                @if ($errors->has('building_insulated_glazings.' . $measureApplication->id . '.insulated_glazing_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('building_insulated_glazings.' . $measureApplication->id . '.insulated_glazing_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class=" col-sm-3 ">
                            <div class="form-group add-space {{ $errors->has('building_insulated_glazings.' . $measureApplication->id . '.building_heating_id') ? ' has-error' : '' }}">
                                <label class=" control-label">
                                    <i data-toggle="collapse" data-target="#building_insulated_glazings_{{ $measureApplication->id }}-building_heating_id-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                    {{\App\Helpers\Translation::translate('insulated-glazing.heated-rooms.title')}}
                                </label>

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'select', 'inputValues' => $heatings, 'userInputValues' => $currentMeasureBuildingInsulatedGlazingForMe ,'userInputColumn' => 'building_heating_id'])
                                <select class="form-control" name="building_insulated_glazings[{{ $measureApplication->id }}][building_heating_id]">
                                    @foreach($heatings as $heating)
                                        <option @if($heating->id == old('building_insulated_glazings.' . $measureApplication->id . '.building_heating_id') || (array_key_exists($measureApplication->id, $buildingInsulatedGlazings) && $buildingInsulatedGlazings[$measureApplication->id]->building_heating_id == $heating->id)) selected="selected" @endif value="{{ $heating->id }}">{{ $heating->name }}</option>
                                    @endforeach
                                </select>
                                @endcomponent

                                <div id="building_insulated_glazings_{{ $measureApplication->id }}-building_heating_id-info"
                                     class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    {{\App\Helpers\Translation::translate('insulated-glazing.heated-rooms.help')}}
                                </div>

                                @if ($errors->has('building_insulated_glazings.' . $measureApplication->id . '.building_heating_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('building_insulated_glazings.' . $measureApplication->id . '.building_heating_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class=" col-sm-3 ">
                            <div class="form-group add-space {{ $errors->has('building_insulated_glazings.' . $measureApplication->id . '.m2') ? ' has-error' : '' }}">
                                <label class=" control-label">
                                    <i data-toggle="collapse" data-target="#building_insulated_glazings_{{ $measureApplication->id }}-m2-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                    {{\App\Helpers\Translation::translate('insulated-glazing.m2.title')}}
                                </label> <span> *</span>

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'input', 'userInputValues' => $currentMeasureBuildingInsulatedGlazingForMe ,'userInputColumn' => 'm2'])
                                    <input type="text" name="building_insulated_glazings[{{ $measureApplication->id }}][m2]" value="{{ old('building_insulated_glazings.' . $measureApplication->id . '.m2', array_key_exists($measureApplication->id, $buildingInsulatedGlazings) ? $buildingInsulatedGlazings[$measureApplication->id]->m2 : '') }}" class="form-control">
                                @endcomponent

                                <div id="building_insulated_glazings_{{ $measureApplication->id }}-m2-info"
                                     class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    {{\App\Helpers\Translation::translate('insulated-glazing.m2.help')}}
                                </div>

                                @if ($errors->has('building_insulated_glazings.' . $measureApplication->id . '.m2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('building_insulated_glazings.' . $measureApplication->id . '.m2') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class=" col-sm-3 ">
                            <div class="form-group add-space {{ $errors->has('building_insulated_glazings.' . $measureApplication->id . '.windows') ? ' has-error' : '' }}">
                                <label class=" control-label">
                                    <i data-toggle="collapse" data-target="#building_insulated_glazings_{{ $measureApplication->id }}-windows-info"
                                       class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                    {{\App\Helpers\Translation::translate('insulated-glazing.total-windows.title')}}
                                </label> <span> *</span>

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'input', 'userInputValues' => $currentMeasureBuildingInsulatedGlazingForMe ,'userInputColumn' => 'windows'])
                                    <input type="text" name="building_insulated_glazings[{{ $measureApplication->id }}][windows]" value="{{ old('building_insulated_glazings.' . $measureApplication->id . '.windows', array_key_exists($measureApplication->id, $buildingInsulatedGlazings) ? $buildingInsulatedGlazings[$measureApplication->id]->windows : '') }}" class="form-control">
                                @endcomponent
                                <div id="building_insulated_glazings_{{ $measureApplication->id }}-windows-info"
                                     class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    {{\App\Helpers\Translation::translate('insulated-glazing.total-windows.help')}}
                                </div>

                                @if ($errors->has('building_insulated_glazings.' . $measureApplication->id . '.windows'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('building_insulated_glazings.' . $measureApplication->id . '.windows') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>

        <hr>
        <div id="remaining-questions">
            <div class="row">
                <div class="col-sm-12">
                    <h4 style="margin-left: -5px;">{{\App\Helpers\Translation::translate('insulated-glazing.cracking-seal.title')}}</h4>
                    <div class="form-group add-space {{ $errors->has('building_elements.'.$crackSealing->id.'.crack-sealing') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#building-elements-crack-sealing-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            {{\App\Helpers\Translation::translate('insulated-glazing.moving-parts-quality.title')}}
                        </label>

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $crackSealing->values()->orderBy('order')->get(), 'userInputValues' => $building->getBuildingElementsForMe('crack-sealing'), 'userInputColumn' => 'element_value_id'])
                        <select class="form-control" name="building_elements[{{$crackSealing->id}}][crack-sealing]">
                            @foreach($crackSealing->values()->orderBy('order')->get() as $sealingValue)
                                <option @if($sealingValue->id == old('building_elements.crack-sealing') || ($building->getBuildingElement('crack-sealing') instanceof \App\Models\BuildingElement && $building->getBuildingElement('crack-sealing')->element_value_id == $sealingValue->id)) selected @endif value="{{ $sealingValue->id }}">{{ $sealingValue->value }}</option>
                            @endforeach
                        </select>
                        @endcomponent


                        <div id="building-elements-crack-sealing-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            {{\App\Helpers\Translation::translate('insulated-glazing.moving-parts-quality.help')}}
                        </div>

                        @if ($errors->has('building_elements.crack-sealing'))
                            <span class="help-block">
                                <strong>{{ $errors->first('building_elements.crack-sealing') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div id="paint-work">
            <div class="row">
                <div class="col-sm-12">
                    <hr>
                    <h4 style="margin-left: -5px;" >{{\App\Helpers\Translation::translate('insulated-glazing.paint-work.title')}} </h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space {{ $errors->has('window_surface') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#window-surface-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            {{\App\Helpers\Translation::translate('insulated-glazing.windows-surface.title')}}
                        </label>

                        @component('cooperation.tool.components.input-group',
                       ['inputType' => 'input', 'userInputValues' => $buildingFeaturesForMe, 'userInputColumn' => 'window_surface'])
                            <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.square-meters.title')}}</span>
                            <input type="text" name="window_surface"  value="{{ old('window_surface') || isset($building->buildingFeatures->window_surface) ? $building->buildingFeatures->window_surface : '' }}" class="form-control">
                        @endcomponent
                        <div id="window-surface-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            {{\App\Helpers\Translation::translate('insulated-glazing.windows-surface.help')}}
                        </div>

                        @if ($errors->has('window_surface'))
                            <span class="help-block">
                                <strong>{{ $errors->first('window_surface') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space {{ $errors->has('building_elements.'.$frames->id.'.frames') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#which-frames-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            {{\App\Helpers\Translation::translate('insulated-glazing.paint-work.which-frames.title')}}
                        </label>

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $frames->values()->orderBy('order')->get(), 'userInputValues' => $building->getBuildingElementsForMe('frames'), 'userInputColumn' => 'element_value_id'])
                        <select class="form-control" name="building_elements[{{$frames->id}}][frames]">
                            @foreach($frames->values()->orderBy('order')->get() as $frameValue)
                                <option @if($frameValue->id == old('building_elements.frames')  || ($building->getBuildingElement('frames') instanceof \App\Models\BuildingElement && $building->getBuildingElement('frames')->element_value_id == $frameValue->id)) selected @endif value="{{ $frameValue->id }}">{{ $frameValue->value }}</option>
                            @endforeach
                        </select>
                        @endcomponent

                        <div id="which-frames-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            {{\App\Helpers\Translation::translate('insulated-glazing.paint-work.which-frames.help')}}
                        </div>

                        @if ($errors->has('building_elements.frames'))
                            <span class="help-block">
                            <strong>{{ $errors->first('building_elements.frames') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space {{ $errors->has('building_elements.'.$woodElements->id.'.wood-elements') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#wood-elements-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            {{\App\Helpers\Translation::translate('insulated-glazing.paint-work.other-wood-elements.title')}}
                        </label>

                        <div id="wood-elements-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            {{\App\Helpers\Translation::translate('insulated-glazing.paint-work.other-wood-elements.help')}}
                        </div>

                        @if ($errors->has('building_elements.wood-elements'))
                            <span class="help-block">
                                <strong>{{ $errors->first('building_elements.wood-elements') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group add-space">


                        <?php
                            // TODO: should do something with a component
                            // the current problem is there are only 2 places where checkboxes are used and those are used in a different way
                        ?>
                        <div class="input-group input-source-group">
                            @foreach($woodElements->values()->orderBy('order')->get() as $woodElement)
                                <label for="building_elements.wood-elements.{{ $woodElement->id }}" class="checkbox-inline">
                                    <input
                                            @if(old('building_elements.wood-elements.'.$woodElements->id.''.$woodElement->id.''))
                                            checked
                                            @elseif($building->buildingElements()->where('element_id', $woodElements->id)->where('element_value_id', $woodElement->id)->first() != null
                                            && $building->buildingElements()->where('element_id', $woodElements->id)->where('element_value_id', $woodElement->id)->first()->element_value_id == $woodElement->id)
                                            checked
                                            @endif
                                            type="checkbox" id="building_elements.wood-elements.{{ $woodElement->id }}" value="{{$woodElement->id}}" name="building_elements[wood-elements][{{ $woodElements->id }}][{{$woodElement->id}}]">
                                    {{ $woodElement->value }}
                                </label>
                            @endforeach
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    @foreach ($woodElements->values()->orderBy('order')->get() as $woodElement)
                                        <?php $notNull = $myBuildingElements->where('element_id', $woodElements->id)->where('element_value_id', $woodElement->id)->first() != null; ?>
                                        @if ($notNull && $myBuildingElements->where('element_id', $woodElements->id)->where('element_value_id', $woodElement->id)->first()->element_value_id == $woodElement->id)
                                            <li><a href="#">{{$myBuildingElements->where('element_id', $woodElements->id)->where('element_value_id', $woodElement->id)->first()->getInputSourceName()}}: {{$woodElement->value}}</a></li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group add-space {{ $errors->has('building_paintwork_statuses.last_painted_year') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#building-paintwork-statuses-last-painted-year-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            {{\App\Helpers\Translation::translate('insulated-glazing.paint-work.last-paintjob.title')}}
                        </label>

                        @component('cooperation.tool.components.input-group',
                               ['inputType' => 'input', 'userInputValues' => $building->currentPaintworkStatus()->forMe()->get() ,'userInputColumn' => 'last_painted_year'])
                                <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
                                <input type="text" name="building_paintwork_statuses[last_painted_year]" class="form-control" value="{{ old('building_paintwork_statuses.last_painted_year', $building->currentPaintworkStatus instanceof \App\Models\BuildingPaintworkStatus ? $building->currentPaintworkStatus->last_painted_year : '') }}">
                        @endcomponent

                        <div id="building-paintwork-statuses-last-painted-year-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            {{\App\Helpers\Translation::translate('insulated-glazing.paint-work.last-paintjob.help')}}
                        </div>

                        @if ($errors->has('building-paintwork-statuses-last-painted-year'))
                            <span class="help-block">
                            <strong>{{ $errors->first('building_paintwork_statuses.last_painted_year') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group add-space {{ $errors->has('building_paintwork_statuses.paintwork_status_id') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#building-paintwork-statuses-paintwork-status-id-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            {{\App\Helpers\Translation::translate('insulated-glazing.paint-work.paint-damage-visible.title')}}
                        </label>

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $paintworkStatuses, 'userInputValues' => $building->currentPaintworkStatus()->forMe()->get(), 'userInputColumn' => 'paintwork_status_id'])
                            <select class="form-control" name="building_paintwork_statuses[paintwork_status_id]">
                                @foreach($paintworkStatuses as $paintworkStatus)
                                    <option @if($paintworkStatus->id == old('building_paintwork_statuses.paintwork_status_id') || ($building->currentPaintworkStatus instanceof \App\Models\BuildingPaintworkStatus && $building->currentPaintworkStatus->paintwork_status_id == $paintworkStatus->id) ) selected @endif value="{{ $paintworkStatus->id }}">{{ $paintworkStatus->name }}</option>
                                @endforeach
                            </select>
                        @endcomponent

                        <div id="building-paintwork-statuses-paintwork-status-id-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            {{\App\Helpers\Translation::translate('insulated-glazing.paint-work.paint-damage-visible.help')}}
                        </div>

                        @if ($errors->has('building_paintwork_statuses.paintwork_status_id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('building_paintwork_statuses.paintwork_status_id') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space {{ $errors->has('building_paintwork_statuses.wood_rot_status_id') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#building-paintwork-statuses-wood-rot-status-id-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            {{\App\Helpers\Translation::translate('insulated-glazing.paint-work.wood-rot-visible.title') }}
                        </label>

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $woodRotStatuses, 'userInputValues' => $building->currentPaintworkStatus()->forMe()->get(), 'userInputColumn' => 'wood_rot_status_id'])
                            <select class="form-control" name="building_paintwork_statuses[wood_rot_status_id]">
                                @foreach($woodRotStatuses as $woodRotStatus)
                                    <option @if($woodRotStatus->id == old('building_paintwork_statuses.wood_rot_status_id') || ($building->currentPaintworkStatus instanceof \App\Models\BuildingPaintworkStatus && $building->currentPaintworkStatus->wood_rot_status_id == $woodRotStatus->id) ) selected @endif value="{{ $woodRotStatus->id }}">{{ $woodRotStatus->name }}</option>
                                @endforeach
                            </select>
                        @endcomponent

                        <div id="building-paintwork-statuses-wood-rot-status-id-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            {{\App\Helpers\Translation::translate('insulated-glazing.paint-work.wood-rot-visible.help')}}
                        </div>

                        @if ($errors->has('building_paintwork_statuses.wood_rot_status_id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('building_paintwork_statuses.wood_rot_status_id') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space {{ $errors->has('comments') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#comments-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('default.form.input.comment')
                        </label>

                        <?php
                            // We do this because we store the comment with every glazing
                            $glazingWithComment = collect($buildingInsulatedGlazings)->where('extra', '!=', null)->first();
                            $comment = !is_null($glazingWithComment) && array_key_exists('comment', $glazingWithComment->extra) ? $glazingWithComment->extra['comment'] : '';
                        ?>

                        <textarea name="comment" id="" class="form-control">{{ $comment }}</textarea>

                        <div id="comments-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            {{\App\Helpers\Translation::translate('general.specific-situation.help')}}
                        </div>

                        @if ($errors->has('comments'))
                            <span class="help-block">
                                <strong>{{ $errors->first('comments') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-12">
                    {{--loop through all the insulated glazings with ALL the input sources--}}
                    @foreach ($buildingInsulatedGlazingsForMe as $buildingInsulatedGlazingForMe)
                        <?php $coachInputSource = App\Models\InputSource::findByShort('coach'); ?>
                        @if($buildingInsulatedGlazingForMe->where('input_source_id', $coachInputSource->id)->first() instanceof \App\Models\BuildingInsulatedGlazing && array_key_exists('comment', $buildingInsulatedGlazingForMe->where('input_source_id', $coachInputSource->id)->first()->extra))
                            @component('cooperation.tool.components.alert')
                                {{$buildingInsulatedGlazingForMe->where('input_source_id', $coachInputSource->id)->first()->extra['comment']}}
                            @endcomponent
                            @break
                        @endif
                    @endforeach
                </div>
            </div>

            @if(\App\Models\BuildingService::hasCoachInputSource(collect($currentMeasureBuildingInsulatedGlazingForMe)) && Auth::user()->hasRole('resident'))
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group add-space{{ $errors->has('comment') ? ' has-error' : '' }}">
                            <?php
                                $coachInputSource = \App\Models\BuildingService::getCoachInput(collect($currentMeasureBuildingInsulatedGlazingForMe));
                                $comment = is_array($coachInputSource->extra) && array_key_exists('comment', $coachInputSource->extra) ? $coachInputSource->extra['comment'] : '';
                            ?>
                            <label for="" class=" control-label"><i data-toggle="collapse" data-target="#comment" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('default.form.input.comment') ({{$coachInputSource->getInputSourceName()}})
                            </label>

                            <textarea disabled="disabled" class="disabled form-control">{{$comment}}</textarea>
                        </div>
                    </div>
                </div>
            @elseif(\App\Models\BuildingService::hasResidentInputSource(collect($currentMeasureBuildingInsulatedGlazingForMe)) && Auth::user()->hasRole('coach'))
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group add-space">
                            <?php
                                $residentInputSource = \App\Models\BuildingService::getResidentInput(collect($currentMeasureBuildingInsulatedGlazingForMe));
                                $comment = is_array($residentInputSource->extra) && array_key_exists('comment', $residentInputSource->extra) ? $residentInputSource->extra['comment'] : '';
                            ?>
                            <label for="" class=" control-label"><i data-toggle="collapse" data-target="#comment" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('default.form.input.comment') ({{$residentInputSource->getInputSourceName()}})
                            </label>

                            <textarea disabled="disabled" class="disabled form-control">{{$comment}}</textarea>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div id="indication-for-costs">
            <hr>
            @include('cooperation.layouts.section-title', ['translationKey' => 'general.costs.cost-and-benefits'])

            <div id="costs" class="row">
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.gas')
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.co2')
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.savings-in-euro')
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.indicative-costs')
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.comparable-rent')
                </div>
            </div>
        </div>

        <div id="taking-into-account">
            <hr>
            @include('cooperation.layouts.section-title', ['translationKey' => 'insulated-glazing.taking-into-account.title'])

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group add-space">
                        <label class="control-label">{{\App\Helpers\Translation::translate('insulated-glazing.taking-into-account.paintwork.title')}}</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                            <input type="text" id="paintwork_costs" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group add-space">
                        <label class="control-label">{{\App\Helpers\Translation::translate('insulated-glazing.taking-into-account.paintwork_year.title')}}</label>
                        <div class="input-group">
                            <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
                            <input type="text" id="paintwork_year" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">@lang('default.buttons.download')</div>
                    <div class="panel-body">
                        <ol>
                            <li><a download="" href="{{ asset('storage/hoomdossier-assets/Maatregelblad_Glasisolatie.pdf') }}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Glasisolatie.pdf')))))}}</a></li>
                            <li><a download="" href="{{ asset('storage/hoomdossier-assets/Maatregelblad_Kierdichting_bouwdelen.pdf') }}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Kierdichting_bouwdelen.pdf')))))}}</a></li>
                            <li><a download="" href="{{ asset('storage/hoomdossier-assets/Maatregelblad_Kierdichting_ramen_en_deuren.pdf') }}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Kierdichting_ramen_en_deuren.pdf')))))}}</a></li>

                        </ol>
                    </div>
                </div>
                <hr>
                <div class="form-group add-space">
                    <div class="">
                        <a class="btn btn-success pull-left" href="{{ route('cooperation.tool.index', [ 'cooperation' => $cooperation ]) }}">@lang('default.buttons.prev')</a>
                        <button type="submit" class="btn btn-primary pull-right">
                            @lang('default.buttons.next')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection

@push('js')
    <script>
        $(document).ready(function() {

            $(window).keydown(function(event){
                if(event.keyCode === 13) {
                    event.preventDefault();
                    return false;
                }
            });

            $("select, input[type=radio], input[type=text], input[type=checkbox]").change(function () {

                var form = $(this).closest("form").serialize();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.insulated-glazing.calculate', [ 'cooperation' => $cooperation ]) }}',
                    data: form,
                    success: function (data) {

                        if (data.hasOwnProperty('savings_gas')){
                            $("input#savings_gas").val(Math.round(data.savings_gas));
                        }
                        if (data.hasOwnProperty('savings_co2')){
                            $("input#savings_co2").val(Math.round(data.savings_co2));
                        }
                        if (data.hasOwnProperty('savings_money')){
                            $("input#savings_money").val(Math.round(data.savings_money));
                        }
                        if (data.hasOwnProperty('cost_indication')){
                            $("input#cost_indication").val(Math.round(data.cost_indication));
                        }
                        if (data.hasOwnProperty('interest_comparable')){
                            $("input#interest_comparable").val(data.interest_comparable);
                        }
                        if (data.hasOwnProperty('paintwork')){
                            $("input#paintwork_costs").val(Math.round(data.paintwork.costs));
                        }
                        if (data.hasOwnProperty('paintwork')){
                            $("input#paintwork_year").val(data.paintwork.year);
                        }

                        @if(App::environment('local'))
                        console.log(data);
                        @endif
                    }
                });
            });

            $('.user-interest').change(function() {

                // the input field
                var userInterest = $(this);
                // the text from the selected interested option
                var userInterestText = userInterest.find('option:selected').text().trim();

                if (userInterestText === "Geen actie" || userInterestText === "Niet mogelijk") {
                    userInterest.parent().parent().parent().find('.values').hide();
                } else {
                    userInterest.parent().parent().parent().find('.values').show();
                }
            });
            // Trigger the change event so it will load the data
            //$("select, input[type=radio], input[type=text]").trigger('change');
            $('form').find('*').filter(':input:visible:first').trigger('change');
        });

    </script>
@endpush