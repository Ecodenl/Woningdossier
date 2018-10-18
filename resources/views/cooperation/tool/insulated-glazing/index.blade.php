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
            {{--@foreach ($keys as $key)--}}
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
                        <h4>@lang('woningdossier.cooperation.tool.insulated-glazing.subtitles.' . $titles[$measureApplication->id])</h4>
                        @endif
                        <div class="form-group add-space {{ $errors->has('user_interests.' . $measureApplication->id) ? ' has-error' : '' }}">
                            <label class=" control-label">
                                <i data-toggle="collapse" data-target="#user_interests_{{ $measureApplication->id }}-info"
                                   class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('woningdossier.cooperation.tool.insulated-glazing.interested-in', ['measure' => lcfirst($measureApplication->measure_name)])
                            </label>

                            <select id="{{ $measureApplication->id }}" class="user-interest form-control" name="user_interests[{{ $measureApplication->id }}]" >
                                @foreach($interests as $interest)
                                    <option @if($interest->id == old('user_interests.' . $measureApplication->id) || (array_key_exists($measureApplication->id, $userInterests) && $interest->id == $userInterests[$measureApplication->id]))  selected="selected" @elseif(Auth::user()->getInterestedType('measure_application', $measureApplication->id) != null && Auth::user()->getInterestedType('measure_application', $measureApplication->id)->interest_id == $interest->id) selected @endif value="{{ $interest->id }}">{{ $interest->name }}</option>
                                @endforeach
                            </select>

                            <div id="user_interests_{{ $measureApplication->id }}-info"
                                 class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
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
                                    @lang('woningdossier.cooperation.tool.insulated-glazing.current-glass')
                                </label>
                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'select', 'inputValues' => $insulatedGlazings, 'userInputValues' => $currentMeasureBuildingInsulatedGlazingForMe ,'userInputColumn' => 'insulating_glazing_id'])
                                    <select class="form-control" name="building_insulated_glazings[{{ $measureApplication->id }}][insulated_glazing_id]">
                                        @foreach($insulatedGlazings as $insulateGlazing)
                                            <option @if($insulateGlazing->id == old('building_insulated_glazings.' . $measureApplication->id . '.insulated_glazing_id') || (array_key_exists($measureApplication->id, $buildingInsulatedGlazings) && $buildingInsulatedGlazings[$measureApplication->id]->insulating_glazing_id == $insulateGlazing->id)) selected @endif value="{{ $insulateGlazing->id }}">{{ $insulateGlazing->name }}</option>
                                        @endforeach
                                    </select>
                                @endcomponent

                                <div id="building_insulated_glazings_{{ $measureApplication->id }}-insulated_glazing_id-info"
                                     class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    And i would like to have it to...
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
                                    @lang('woningdossier.cooperation.tool.insulated-glazing.heated-rooms')
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
                                    And i would like to have it to...
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
                                    @lang('woningdossier.cooperation.tool.insulated-glazing.m2')
                                </label> <span> *</span>

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'input', 'userInputValues' => $currentMeasureBuildingInsulatedGlazingForMe ,'userInputColumn' => 'm2'])
                                    <input type="text" name="building_insulated_glazings[{{ $measureApplication->id }}][m2]" value="{{ old('building_insulated_glazings.' . $measureApplication->id . '.m2', array_key_exists($measureApplication->id, $buildingInsulatedGlazings) ? $buildingInsulatedGlazings[$measureApplication->id]->m2 : '') }}" class="form-control">
                                @endcomponent

                                <div id="building_insulated_glazings_{{ $measureApplication->id }}-m2-info"
                                     class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    And i would like to have it to...
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
                                    @lang('woningdossier.cooperation.tool.insulated-glazing.total-windows')
                                </label> <span> *</span>

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'input', 'userInputValues' => $currentMeasureBuildingInsulatedGlazingForMe ,'userInputColumn' => 'windows'])
                                    <input type="text" name="building_insulated_glazings[{{ $measureApplication->id }}][windows]" value="{{ old('building_insulated_glazings.' . $measureApplication->id . '.windows', array_key_exists($measureApplication->id, $buildingInsulatedGlazings) ? $buildingInsulatedGlazings[$measureApplication->id]->windows : '') }}" class="form-control">
                                @endcomponent
                                <div id="building_insulated_glazings_{{ $measureApplication->id }}-windows-info"
                                     class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    And i would like to have it to...
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
                    <h4 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.insulated-glazing.cracking-seal.title')</h4>
                    <div class="form-group add-space {{ $errors->has('building_elements.'.$crackSealing->id.'.crack-sealing') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#building_elements.crack-sealing-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.insulated-glazing.moving-parts-quality')
                        </label>

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $crackSealing->values()->orderBy('order')->get(), 'userInputValues' => $building->getBuildingElementsForMe('crack-sealing'), 'userInputColumn' => 'element_value_id'])
                        <select class="form-control" name="building_elements[{{$crackSealing->id}}][crack-sealing]">
                            @foreach($crackSealing->values()->orderBy('order')->get() as $sealingValue)
                                <option @if($sealingValue->id == old('building_elements.crack-sealing') || ($building->getBuildingElement('crack-sealing') instanceof \App\Models\BuildingElement && $building->getBuildingElement('crack-sealing')->element_value_id == $sealingValue->id)) selected @endif value="{{ $sealingValue->id }}">{{ $sealingValue->value }}</option>
                            @endforeach
                        </select>
                        @endcomponent

                        <div id="building_elements.crack-sealing-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
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
                    <h4 style="margin-left: -5px;" >@lang('woningdossier.cooperation.tool.insulated-glazing.paint-work.title') </h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space {{ $errors->has('window_surface') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#window-surface-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.insulated-glazing.windows-surface')
                        </label>

                        @component('cooperation.tool.components.input-group',
                       ['inputType' => 'input', 'userInputValues' => $buildingFeaturesForMe, 'userInputColumn' => 'window_surface'])
                            <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.square-meters')</span>
                            <input type="text" name="window_surface"  value="{{ old('window_surface') || isset($building->buildingFeatures->window_surface) ? $building->buildingFeatures->window_surface : '' }}" class="form-control">
                        @endcomponent
                        <div id="window-surface-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
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
                            @lang('woningdossier.cooperation.tool.insulated-glazing.paint-work.which-frames')
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
                            And i would like to have it to...
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
                            @lang('woningdossier.cooperation.tool.insulated-glazing.paint-work.other-wood-elements')
                        </label>

                        <div id="wood-element-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
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
                            <i data-toggle="collapse" data-target="#building_paintwork_statuses.last_painted_year-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.insulated-glazing.paint-work.last-paintjob')
                        </label> <span>*</span>

                        @component('cooperation.tool.components.input-group',
                               ['inputType' => 'input', 'userInputValues' => $building->currentPaintworkStatus()->forMe()->get() ,'userInputColumn' => 'last_painted_year'])
                                <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.year')</span>
                                <input required type="text" name="building_paintwork_statuses[last_painted_year]" class="form-control" value="{{ old('building_paintwork_statuses.last_painted_year', $building->currentPaintworkStatus instanceof \App\Models\BuildingPaintworkStatus ? $building->currentPaintworkStatus->last_painted_year : '') }}">
                        @endcomponent

                        <div id="building_paintwork_statuses.last_painted_year" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('building_paintwork_statuses.last_painted_year'))
                            <span class="help-block">
                            <strong>{{ $errors->first('building_paintwork_statuses.last_painted_year') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group add-space {{ $errors->has('building_paintwork_statuses.paintwork_status_id') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#building_paintwork_statuses.paintwork_status_id-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.insulated-glazing.paint-work.paint-damage-visible')
                        </label>

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $paintworkStatuses, 'userInputValues' => $building->currentPaintworkStatus()->forMe()->get(), 'userInputColumn' => 'paintwork_status_id'])
                            <select class="form-control" name="building_paintwork_statuses[paintwork_status_id]">
                                @foreach($paintworkStatuses as $paintworkStatus)
                                    <option @if($paintworkStatus->id == old('building_paintwork_statuses.paintwork_status_id') || ($building->currentPaintworkStatus instanceof \App\Models\BuildingPaintworkStatus && $building->currentPaintworkStatus->paintwork_status_id == $paintworkStatus->id) ) selected @endif value="{{ $paintworkStatus->id }}">{{ $paintworkStatus->name }}</option>
                                @endforeach
                            </select>
                        @endcomponent

                        <div id="building_paintwork_statuses.paintwork_status_id-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
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
                            <i data-toggle="collapse" data-target="#building_paintwork_statuses.wood_rot_status_id-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.insulated-glazing.paint-work.wood-rot-visible')
                        </label>

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $woodRotStatuses, 'userInputValues' => $building->currentPaintworkStatus()->forMe()->get(), 'userInputColumn' => 'wood_rot_status_id'])
                            <select class="form-control" name="building_paintwork_statuses[wood_rot_status_id]">
                                @foreach($woodRotStatuses as $woodRotStatus)
                                    <option @if($woodRotStatus->id == old('building_paintwork_statuses.wood_rot_status_id') || ($building->currentPaintworkStatus instanceof \App\Models\BuildingPaintworkStatus && $building->currentPaintworkStatus->wood_rot_status_id == $woodRotStatus->id) ) selected @endif value="{{ $woodRotStatus->id }}">{{ $woodRotStatus->name }}</option>
                                @endforeach
                            </select>
                        @endcomponent

                        <div id="building_paintwork_statuses.wood_rot_status_id-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
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
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('comments'))
                            <span class="help-block">
                                <strong>{{ $errors->first('comments') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div id="indication-for-costs">
            <hr>
            <h4 style="margin-left: -5px">@lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.title')</h4>

            <div id="costs" class="row">
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.gas-savings')</label>
                        <div class="input-group">
                            <span class="input-group-addon">m<sup>3</sup> / @lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.year')</span>
                            <input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.co2-savings')</label>
                        <div class="input-group">
                            <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.kilograms') / @lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.year')</span>
                            <input type="text" id="savings_co2" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.savings-in-euro')</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i> / @lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.year')</span>
                            <input type="text" id="savings_money" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.indicative-costs')</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                            <input type="text" id="cost_indication" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>

                </div>
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.comparable-rate')</label>
                        <div class="input-group">
                            <span class="input-group-addon">% / @lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.year')</span>
                            <input type="text" id="interest_comparable" class="form-control disabled" disabled="" value="0,0">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="taking-into-account">
            <hr>
            <h4 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.wall-insulation.taking-into-account.title')</h4>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.insulated-glazing.taking-into-account.paintwork')</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                            <input type="text" id="paintwork_costs" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.insulated-glazing.taking-into-account.paintwork_year')</label>
                        <div class="input-group">
                            <span class="input-group-addon">@lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.year')</span>
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
                            <li><a download="" href="{{asset('storage/hoomdossier-assets/Maatregelblad_Glasisolatie.pdf')}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Glasisolatie.pdf')))))}}</a></li>
                            <li><a download="" href="{{asset('storage/hoomdossier-assets/Maatregelblad_Kierdichting_bouwdelen.pdf')}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Kierdichting_bouwdelen.pdf')))))}}</a></li>
                            <li><a download="" href="{{asset('storage/hoomdossier-assets/Maatregelblad_Kierdichting_ramen_en_deuren.pdf')}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Kierdichting_ramen_en_deuren.pdf')))))}}</a></li>
                            <?php $helpFile = "storage/hoomdossier-assets/Invul_hulp_Glasisolatie.pdf"; ?>
                            <li><a download="" href="{{asset($helpFile)}}">{{ ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset($helpFile))))) }}</a></li>

                        </ol>
                    </div>
                </div>
                <hr>
                <div class="form-group add-space">
                    <div class="">
                        <a class="btn btn-success pull-left" href="{{ route('cooperation.tool.wall-insulation.index', [ 'cooperation' => $cooperation ]) }}">@lang('default.buttons.prev')</a>
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
                $('.user-interest option:selected').each(function() {
                    $userInterest = $(this); // the input field
                    if ($userInterest.text() == "Geen actie" || $userInterest.text() == "Niet mogelijk") {
                        $userInterest.parent().parent().parent().next().hide();
                    } else {
                        $userInterest.parent().parent().parent().next().show();
                    }
                });
            });
            // Trigger the change event so it will load the data
            //$("select, input[type=radio], input[type=text]").trigger('change');
            $('form').find('*').filter(':input:visible:first').trigger('change');
        });

    </script>
@endpush