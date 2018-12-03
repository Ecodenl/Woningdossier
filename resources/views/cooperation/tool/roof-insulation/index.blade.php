@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.roof-insulation.title'))

@section('step_content')
    <form class="form-horizontal" method="POST" action="{{ route('cooperation.tool.roof-insulation.store', ['cooperation' => $cooperation]) }}">

        {{csrf_field()}}
        @include('cooperation.tool.includes.interested', ['type' => 'element'])
        <div class="row">
            <div id="current-situation" class="col-md-12">
                @include('cooperation.layouts.section-title', ['translationKey' => 'roof-insulation.title'])

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group add-space {{ $errors->has('building_roof_types') ? ' has-error' : '' }}">
                            <label for="building_roof_types" class="control-label">
                                <i data-toggle="collapse" data-target="#roof-type-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                {{\App\Helpers\Translation::translate('roof-insulation.current-situation.roof-types.title')}}</label>
                            <br>
                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'checkbox', 'inputValues' => $roofTypes, 'userInputValues' => $currentRoofTypesForMe, 'userInputColumn' => 'roof_type_id'])
                                @foreach($roofTypes as $roofType)
                                    <label class="checkbox-inline">
                                        <input data-calculate-value="{{$roofType->calculate_value}}" type="checkbox" name="building_roof_types[]" value="{{ $roofType->id }}"
                                        @if((is_array(old('building_roof_types')) && in_array($roofType->id, old('building_roof_types'))) ||
                                        ($currentRoofTypes->contains('roof_type_id', $roofType->id)) ||
                                        ($features->roofType->id == $roofType->id)) checked @endif>
                                        {{ $roofType->name }}
                                    </label>
                                @endforeach
                            @endcomponent

                            @if ($errors->has('building_roof_types'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('building_roof_types') }}</strong>
                                </span>
                            @endif


                            <div class="col-sm-12">
                                <div class="form-group add-space">
                                    <div id="roof-type-info" class="collapse alert alert-info remove-collapse-space">
                                        {{\App\Helpers\Translation::translate('roof-insulation.current-situation.roof-types.help')}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="if-roof">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group add-space {{$errors->has('building_features.roof_type_id') ? ' has-error' : ''}}">

                                <label for="main_roof" class="control-label"><i data-toggle="collapse" data-target="#main-roof-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                    {{\App\Helpers\Translation::translate('roof-insulation.current-situation.main-roof.title')}}
                                </label>

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'checkbox', 'inputValues' => $roofTypes, 'userInputValues' => $currentRoofTypesForMe, 'userInputColumn' => 'roof_type_id', 'additionalConditionColumn' => true])
                                    <select id="main_roof" class="form-control" name="building_features[roof_type_id]">
                                        @foreach($roofTypes as $roofType)
                                            @if($roofType->calculate_value < 5)
                                            <option @if($roofType->id == old('building_features.roof_type_id') || ($features->roof_type_id == $roofType->id)) selected @endif value="{{ $roofType->id }}">{{ $roofType->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                @endcomponent

                                <div id="main-roof-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                    {{\App\Helpers\Translation::translate('roof-insulation.current-situation.main-roof.help')}}
                                </div>
                                @if ($errors->has('building_features.roof_type_id'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('building_features.roof_type_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @foreach(['flat', 'pitched'] as $roofCat)

                        <div class="{{ $roofCat }}-roof">

                            <h4 style="margin-left: -5px;">{{\App\Helpers\Translation::translate('roof-insulation.'.$roofCat.'-roof.situation-title.title')}}</h4>
                            <div class="row">
                                <div class="col-sm-12 col-md-12">
                                    <div class="form-group add-space {{ $errors->has('building_roof_types.' . $roofCat . '.element_value_id') ? ' has-error' : '' }}">

                                        <label for="flat_roof_insulation" class="control-label">
                                            <i data-toggle="collapse" data-target="#{{ $roofCat }}-roof-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                            {{\App\Helpers\Translation::translate('roof-insulation.current-situation.is-'.$roofCat.'-roof-insulated.title')}}</label>

                                        <select id="flat_roof_insulation" class="form-control" name="building_roof_types[{{ $roofCat }}][element_value_id]" >
                                            @foreach($roofInsulation->values as $insulation)
                                                @if($insulation->calculate_value < 6)
                                                    <option data-calculate-value="{{$insulation->calculate_value}}" @if($insulation->id == old('building_roof_types.' . $roofCat . '.element_value_id') || (isset($currentCategorizedRoofTypes[$roofCat]['element_value_id']) && $currentCategorizedRoofTypes[$roofCat]['element_value_id'] == $insulation->id)) selected @endif value="{{ $insulation->id }}">{{ $insulation->value }}</option>
                                                @endif
                                            @endforeach
                                        </select>

                                        <div id="{{ $roofCat }}-roof-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                            {{\App\Helpers\Translation::translate('roof-insulation.current-situation.is-'.$roofCat.'-roof-insulated.title')}}
                                        </div>
                                        @if ($errors->has('building_roof_types.' . $roofCat . '.element_value_id'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('building_roof_types.' . $roofCat . '.element_value_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div id="{{$roofCat}}-info-alert" class="col-sm-12 col-md-12">
{{--                                    @foreach($roofInsulation->values as $insulation)--}}
{{--                                        @if(isset($currentCategorizedRoofTypes[$roofCat]['element_value_id']) && $currentCategorizedRoofTypes[$roofCat]['element_value_id'] == $insulation->id)--}}
{{--                                            @if(($insulation->calculate_value == 3 || $insulation->calculate_value == 4) && $interest->calculate_value <= 2)--}}
                                                @component('cooperation.tool.components.alert', ['alertType' => 'info', 'hide' => true])
                                                    Hoe veel u met deze maatregel kunt besparen hangt ervan wat de isolatiewaarde van de huidige isolatielaag is.
                                                    Voor het uitrekenen van de daadwerkelijke besparing bij het na- isoleren van een reeds geïsoleerde gevel/vloer/dak is aanvullend en gespecialiseerd advies nodig.
                                                @endcomponent
                                            {{--@endif--}}
                                            {{--@break--}}
                                        {{--@endif--}}
                                    {{--@endforeach--}}
                                </div>
                            </div>
                            <div class="{{$roofCat}}-hideable">
                                <div class="row">
                                    <div class="col-sm-12 col-md-6 roof-surface-inputs">
                                        <div class="form-group add-space {{ $errors->has('building_roof_types.' . $roofCat . '.roof_surface') ? ' has-error' : '' }}">

                                            <label for="flat-roof-surfaces" class=" control-label">
                                            <i data-toggle="collapse" data-target="#{{ $roofCat }}-surface-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                            {{\App\Helpers\Translation::translate('roof-insulation.current-situation.'.$roofCat.'-roof-surface.title')}}</label> <span> *</span>

                                            @component('cooperation.tool.components.input-group',
                                            ['inputType' => 'input', 'userInputValues' => $currentCategorizedRoofTypesForMe[$roofCat], 'userInputColumn' => 'roof_surface'])
                                            <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.square-meters')</span>
                                            <input type="text" class="form-control" name="building_roof_types[{{ $roofCat }}][roof_surface]" value="{{isset($currentCategorizedRoofTypes[$roofCat]['roof_surface']) ? $currentCategorizedRoofTypes[$roofCat]['roof_surface'] : old('building_roof_types.' . $roofCat . '.roof_surface')}}">
                                        @endcomponent

                                            <div id="{{ $roofCat }}-surface-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                                {{\App\Helpers\Translation::translate('roof-insulation.current-situation.'.$roofCat.'-roof-surface.help')}}
                                            </div>

                                            @if ($errors->has('building_roof_types.' . $roofCat . '.roof_surface'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('building_roof_types.' . $roofCat . '.roof_surface') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <div class="form-group add-space {{ $errors->has('building_roof_types.' . $roofCat . '.insulation_roof_surface') ? ' has-error' : '' }}">

                                        <label for="flat-roof-surfaces" class=" control-label">
                                            <i data-toggle="collapse" data-target="#{{ $roofCat }}-insulation_roof_surface-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                            {{ \App\Helpers\Translation::translate("roof-insulation.current-situation.insulation-".$roofCat."-roof-surface.title") }}
                                        </label> <span> *</span>


                                        @component('cooperation.tool.components.input-group',
                                        ['inputType' => 'input', 'userInputValues' => $currentCategorizedRoofTypesForMe[$roofCat], 'userInputColumn' => 'insulation_roof_surface'])
                                                <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.square-meters')</span>
                                                <input type="text"  class="form-control" name="building_roof_types[{{ $roofCat }}][insulation_roof_surface]" value="{{isset($currentCategorizedRoofTypes[$roofCat]['insulation_roof_surface']) ? $currentCategorizedRoofTypes[$roofCat]['insulation_roof_surface'] : old('building_roof_types.' . $roofCat . '.insulation_roof_surface')}}">
                                            @endcomponent

                                            <div id="{{ $roofCat }}-insulation_roof_surface-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                                {{\App\Helpers\Translation::translate("roof-insulation.current-situation.insulation-".$roofCat."-roof-surface.help")}}
                                            </div>

                                        @if ($errors->has('building_roof_types.' . $roofCat . '.insulation_roof_surface'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('building_roof_types.' . $roofCat . '.insulation_roof_surface') }}</strong>
                                            </span>
                                        @endif
                                        </div>
                                    </div>
                                </div>
                                {{-- Had class .cover-zinc not used in js, does not seem neseserie --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group add-space {{ $errors->has('building_roof_types.' . $roofCat . '.extra.zinc_replaced_date') ? ' has-error' : '' }}">
                                            <label for="zinc-replaced" class="control-label">
                                                <i data-toggle="collapse" data-target="#zinc-{{$roofCat}}-replaced-date" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                                {{\App\Helpers\Translation::translate('roof-insulation.current-situation.zinc-replaced.title')}}
                                            </label>

                                                @component('cooperation.tool.components.input-group',
                                                ['inputType' => 'input', 'userInputValues' => $currentCategorizedRoofTypesForMe[$roofCat], 'userInputColumn' => 'extra.zinc_replaced_date'])
                                                <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.year')</span>
                                                <input type="text" class="form-control" name="building_roof_types[{{ $roofCat }}][extra][zinc_replaced_date]" value="{{ isset($currentCategorizedRoofTypes[$roofCat]['extra']['zinc_replaced_date']) ? $currentCategorizedRoofTypes[$roofCat]['extra']['zinc_replaced_date'] : old('building_roof_types.' . $roofCat . '.extra.zinc_replaced_date') }}">
                                                @endcomponent


                                            <div id="zinc-{{$roofCat}}-replaced-date" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                                    {{\App\Helpers\Translation::translate("roof-insulation.current-situation.zinc-replaced.help")}}
                                                </div>

                                            @if ($errors->has('building_roof_types.' . $roofCat . '.extra.zinc_replaced_date'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('building_roof_types.' . $roofCat . '.extra.zinc_replaced_date') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row cover-bitumen">
                                <div class="col-md-12">
                                    <div class="form-group add-space {{ $errors->has('building_roof_types.' . $roofCat . '.extra.bitumen_replaced_date') ? ' has-error' : '' }}">
                                        <label for="bitumen-replaced" class=" control-label">
                                            <i data-toggle="collapse" data-target="#bitumen-replaced-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                            {{\App\Helpers\Translation::translate('roof-insulation.current-situation.bitumen-insulated.title')}}</label>

				                        <?php
				                            $default = (isset($currentCategorizedRoofTypes[$roofCat]['extra']['bitumen_replaced_date']) && $currentCategorizedRoofTypes[$roofCat]['extra']['bitumen_replaced_date'] != 1) ? $currentCategorizedRoofTypes[$roofCat]['extra']['bitumen_replaced_date'] : '';
				                        ?>

                                            @component('cooperation.tool.components.input-group',
                                            ['inputType' => 'input', 'userInputValues' => $currentCategorizedRoofTypesForMe[$roofCat], 'userInputColumn' => 'extra.bitumen_replaced_date'])
                                            <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.year')</span>
                                            <input type="text" class="form-control" name="building_roof_types[{{ $roofCat }}][extra][bitumen_replaced_date]" value="{{ old('building_roof_types.' . $roofCat . '.extra.bitumen_replaced_date', $default) }}">
                                        @endcomponent

                                            <div id="bitumen-replaced-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                                {{\App\Helpers\Translation::translate('roof-insulation.current-situation.bitumen-insulated.help')}}
                                            </div>

                                            @if ($errors->has('building_roof_types.' . $roofCat . '.extra.bitumen_replaced_date'))
                                                <span class="help-block">
                                                        <strong>{{ $errors->first('building_roof_types.' . $roofCat . '.extra.bitumen_replaced_date') }}</strong>
                                                    </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if($roofCat == 'pitched')

                                    <div class="row cover-tiles">
                                        <div class="col-md-12">
                                            <div class="form-group add-space {{ $errors->has('building_roof_types.' . $roofCat . '.extra.tiles_condition') ? ' has-error' : '' }}">

                                                <label for="tiles_condition" class=" control-label">
                                                <i data-toggle="collapse" data-target="#tiles-condition-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                                {{\App\Helpers\Translation::translate('roof-insulation.current-situation.in-which-condition-tiles.title')}}</label>

                                                <?php
                                                    $default = (isset($currentCategorizedRoofTypes[$roofCat]['extra']['tiles_condition']) && $currentCategorizedRoofTypes[$roofCat]['extra']['tiles_condition'] != 1) ? $currentCategorizedRoofTypes[$roofCat]['extra']['tiles_condition'] : '';
                                                ?>

                                                @component('cooperation.tool.components.input-group',
                                            ['inputType' => 'select', 'inputValues' => $roofTileStatuses, 'userInputValues' => $currentCategorizedRoofTypesForMe[$roofCat] ,'userInputColumn' => 'extra.tiles_condition'])
                                                <select  id="tiles_condition" class="form-control" name="building_roof_types[{{ $roofCat }}][extra][tiles_condition]" >
                                                    @foreach($roofTileStatuses as $roofTileStatus)
                                                        <option @if($roofTileStatus->id == old('building_roof_types.' . $roofCat . '.extra.tiles_condition', $default)) selected  @endif value="{{ $roofTileStatus->id }}">{{ $roofTileStatus->name }}</option>
                                                    @endforeach
                                                </select>
                                            @endcomponent

                                                <div id="tiles-condition-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                                    {{\App\Helpers\Translation::translate('roof-insulation.current-situation.in-which-condition-tiles.help')}}
                                                </div>

                                                @if ($errors->has('building_roof_types.' . $roofCat . '.extra.tiles_condition'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('building_roof_types.' . $roofCat . '.extra.tiles_condition') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                @endif

                            <div class="{{$roofCat}}-hideable">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group add-space {{$errors->has('building_roof_types.' . $roofCat . '.extra.measure_application_id') ? ' has-error' : ''}}">

                                            <label for="building_type_id" class=" control-label">
                                            <i data-toggle="collapse" data-target="#{{ $roofCat }}-interested-roof-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                            {{\App\Helpers\Translation::translate('roof-insulation.'.$roofCat.'-roof.insulate-roof.title')}}</label>

                                            <?php
                                                $default = isset($currentCategorizedRoofTypes[$roofCat]['extra']['measure_application_id']) ? $currentCategorizedRoofTypes[$roofCat]['extra']['measure_application_id'] : 0;
                                            ?>

                                            @component('cooperation.tool.components.input-group',
                                            ['inputType' => 'select', 'inputValues' => $measureApplications[$roofCat], 'userInputValues' => $currentCategorizedRoofTypesForMe[$roofCat] ,'userInputColumn' => 'extra.measure_application_id', 'customInputValueColumn' => 'measure_name'])
                                                <select id="flat_roof_insulation" class="form-control" name="building_roof_types[{{ $roofCat }}][measure_application_id]">
                                                    <option value="0" @if($default == 0) selected @endif>
                                                        {{\App\Helpers\Translation::translate('roof-insulation.measure-application.no.title')}}
                                                    </option>
                                                    @foreach($measureApplications[$roofCat] as $measureApplication)
                                                        <option @if($measureApplication->id == old('building_roof_types.' . $roofCat . '.extra.measure_application_id', $default)) selected @endif value="{{ $measureApplication->id }}">{{ $measureApplication->measure_name }}</option>
                                                    @endforeach
                                                </select>
                                            @endcomponent

                                            <div id="{{ $roofCat }}-interested-roof-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                                {{\App\Helpers\Translation::translate('roof-insulation.'.$roofCat.'-roof.insulate-roof.help')}}
                                            </div>
                                            @if ($errors->has('building_roof_types.' . $roofCat . '.extra.measure_application_id'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('building_roof_types.' . $roofCat . '.extra.measure_application_id') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group add-space {{$errors->has('building_roof_types.' . $roofCat . '.building_heating_id') ? ' has-error' : ''}}">

                                            <label for="building_type_id" class=" control-label">
                                            <i data-toggle="collapse" data-target="#{{ $roofCat }}-heating-roof-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                            {{\App\Helpers\Translation::translate('roof-insulation.'.$roofCat.'-roof.situation.title')}}</label>

                                            <?php
                                            $default = isset($currentCategorizedRoofTypes[$roofCat]['building_heating_id']) ? $currentCategorizedRoofTypes[$roofCat]['building_heating_id'] : 0;
                                            ?>

                                            @component('cooperation.tool.components.input-group',
                                        ['inputType' => 'select', 'inputValues' => $heatings, 'userInputValues' => $currentCategorizedRoofTypesForMe[$roofCat] ,'userInputColumn' => 'building_heating_id'])
                                            <select id="flat_roof_situation" class="form-control" name="building_roof_types[{{ $roofCat }}][building_heating_id]" >
                                                @foreach($heatings as $heating)
                                                    @if($heating->calculate_value < 5)
                                                    <option @if($heating->id == old('building_roof_types.' . $roofCat . '.building_heating_id', $default)) selected @endif value="{{ $heating->id }}">{{ $heating->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        @endcomponent

                                            <div id="{{ $roofCat }}-heating-roof-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                                {{\App\Helpers\Translation::translate('roof-insulation.'.$roofCat.'-roof.situation.help')}}
                                            </div>
                                            @if ($errors->has('building_roof_types.' . $roofCat . '.building_heating_id'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('building_roof_types.' . $roofCat . '.building_heating_id') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group add-space {{ $errors->has('building_roof_types.'.$roofCat.'.extra.comment') ? ' has-error' : '' }}">
                                            <label for="" class="control-label">
                                                <i data-toggle="collapse" data-target="#comments-{{$roofCat}}-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                                {{\App\Helpers\Translation::translate('general.specific-situation.title')}}
                                            </label>

                                            <?php
                                            $default = isset($currentCategorizedRoofTypes[$roofCat]['extra']['comment']) ? $currentCategorizedRoofTypes[$roofCat]['extra']['comment'] : old('building_roof_types.' . $roofCat . '.extra.comment');
                                            ?>


                                            <textarea name="building_roof_types[{{ $roofCat }}][extra][comment]" id="" class="form-control">{{ $default }}</textarea>

                                            <div id="comments-{{$roofCat}}-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                                {{\App\Helpers\Translation::translate('general.specific-situation.help')}}
                                            </div>

                                        @if ($errors->has('building_roof_types.'.$roofCat.'.extra.comment'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('building_roof_types.'.$roofCat.'.extra.comment') }}</strong>
                                            </span>
                                        @endif</div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    {{--loop through all the insulated glazings with ALL the input sources--}}
                                    @foreach ($currentCategorizedRoofTypesForMe[$roofCat] as $currentRoofTypeForMe)
                                        <?php $coachInputSource = App\Models\InputSource::findByShort('coach'); ?>
                                        @if($currentRoofTypeForMe->input_source_id == $coachInputSource->id && array_key_exists('comment', $currentRoofTypeForMe->extra))
                                            @component('cooperation.tool.components.alert')
                                                {{$currentRoofTypeForMe->extra['comment']}}
                                            @endcomponent
                                            @break
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    @endforeach

                    @foreach(['flat', 'pitched'] as $roofCat)
                        <div class="costs {{ $roofCat }}-roof col-md-12">

                            <div class="row">

                                <div class="col-md-12">
                                    @include('cooperation.layouts.section-title', [
                                        'translationKey' => 'roof-insulation.'.$roofCat.'.costs.title',
                                        'infoAlertId' => $roofCat.'costs-info'
                                    ])
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-4 {{$roofCat}}-hideable">
                                    @include('cooperation.layouts.indication-for-costs.gas', ['id' => $roofCat])
                                </div>

                                <div class="col-md-4 {{$roofCat}}-hideable">
                                    @include('cooperation.layouts.indication-for-costs.co2', ['id' => $roofCat])
                                </div>
                                <div class="col-md-4 {{$roofCat}}-hideable">
                                    @include('cooperation.layouts.indication-for-costs.savings-in-euro', ['id' => $roofCat])
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 {{$roofCat}}-hideable">
                                    @include('cooperation.layouts.indication-for-costs.indicative-costs', ['id' => $roofCat])
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group add-space @if($roofCat == 'pitched') cover-tiles @endif">
                                        <label class="control-label">
                                            <i data-toggle="collapse" data-target="#{{$roofCat}}-indicative-costs-replacement-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                            {{\App\Helpers\Translation::translate('roof-insulation.'.$roofCat.'.indicative-costs-replacement.title')}}
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                                            <input type="text" id="{{ $roofCat }}_replace_cost" class="form-control disabled" disabled="" value="0">
                                        </div>
                                        <div id="{{$roofCat}}-indicative-costs-replacement-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                            {{\App\Helpers\Translation::translate('roof-insulation.'.$roofCat.'.indicative-costs-replacement.help')}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 @if($roofCat == 'pitched') cover-tiles @endif">
                                    <div class="form-group add-space">
                                        <label class="control-label">
                                            <i data-toggle="collapse" data-target="#{{$roofCat}}-indicative-replacement-year-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                            {{\App\Helpers\Translation::translate('roof-insulation.'.$roofCat.'.indicative-replacement.year.title')}}
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                            <input type="text" id="{{ $roofCat }}_replace_year" class="form-control disabled" disabled="" value="">
                                        </div>
                                        <div id="{{$roofCat}}-indicative-replacement-year-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                            {{\App\Helpers\Translation::translate('roof-insulation.'.$roofCat.'.indicative-replacement.year.help')}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 {{$roofCat}}-hideable">
                                    @include('cooperation.layouts.indication-for-costs.comparable-rent', ['id' => $roofCat])
                                </div>
                            </div>
                        </div>

                    @endforeach

                </div>

            @foreach(['flat', 'pitched'] as $roofCat)
                @if(\App\Models\BuildingService::hasCoachInputSource(collect($currentCategorizedRoofTypesForMe[$roofCat])) && Auth::user()->hasRole('resident'))
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group add-space{{ $errors->has('comment') ? ' has-error' : '' }}">
                                <?php
                                    $coachInputSource = \App\Models\BuildingService::getCoachInput(collect($currentCategorizedRoofTypesForMe[$roofCat]));
                                    $comment = is_array($coachInputSource->extra) && array_key_exists('comment', $coachInputSource->extra) ? $coachInputSource->extra['comment'] : '';
                                ?>
                                <label for="" class=" control-label"><i data-toggle="collapse" data-target="#comment" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                    @lang('default.form.input.comment') ({{$coachInputSource->getInputSourceName()}}, @lang('woningdossier.cooperation.tool.roof-insulation.' . $roofCat . '-roof.title'))
                                </label>

                            <textarea disabled="disabled" class="disabled form-control">{{$comment}}</textarea>
                        </div>
                    </div>
                </div>
            @elseif(\App\Models\BuildingService::hasResidentInputSource(collect($currentCategorizedRoofTypesForMe[$roofCat])) && Auth::user()->hasRole('coach'))
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group add-space">
						    <?php
						    $residentInputSource = \App\Models\BuildingService::getResidentInput(collect($currentCategorizedRoofTypesForMe[$roofCat]));
						    $comment = is_array($residentInputSource->extra) && array_key_exists('comment', $residentInputSource->extra) ? $residentInputSource->extra['comment'] : '';
						    ?>
                            <label for="" class=" control-label"><i data-toggle="collapse" data-target="#comment" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('default.form.input.comment') ({{$residentInputSource->getInputSourceName()}}, @lang('woningdossier.cooperation.tool.roof-insulation.' . $roofCat . '-roof.title'))
                            </label>

                            <textarea disabled="disabled" class="disabled form-control">{{$comment}}</textarea>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">@lang('default.buttons.download')</div>
                    <div class="panel-body">
                        <ol>
                            <li><a download="" href="{{asset('storage/hoomdossier-assets/Maatregelblad_Dakisolatie.pdf')}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Dakisolatie.pdf')))))}}</a></li>
                        </ol>
                    </div>
                </div>
                <hr>
                <div class="form-group add-space">
                    <div class="">
                        <a class="btn btn-success pull-left" href="{{route('cooperation.tool.floor-insulation.index', ['cooperation' => $cooperation])}}">@lang('default.buttons.prev')</a>
                        <button type="submit" class=" btn btn-primary pull-right">
                            @lang('default.buttons.next')
                        </button>
                    </div>
                </div>
            </div>
        </div>

@endsection

@push('js')
    <script>
        $(document).ready(function() {

            function hide() {

            }
            $(window).keydown(function(event){
                if(event.keyCode === 13) {
                    event.preventDefault();
                    return false;
                }
            });

            $('select[name*=element_value_id]').trigger('change')

            $("select, input[type=radio], input[type=text], input[type=number], input[type=checkbox]").change(formChange);

            function formChange(){



                var form = $(this).closest("form").serialize();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.roof-insulation.calculate', [ 'cooperation' => $cooperation ]) }}',
                    data: form,
                    success: function(data){
                        if (!data.hasOwnProperty('flat') && !data.hasOwnProperty('pitched')){
                            $(".if-roof").hide();
                        }
                        else {

                            $(".if-roof").show();
                        }

                        // default
                        //$(".cover-zinc").hide();
                        $(".flat-roof .cover-bitumen").hide();
                        $(".pitched-roof .cover-bitumen").hide();

                        if (data.hasOwnProperty('flat')){
                            $(".flat-roof").show();
                            $(".flat-roof .cover-bitumen").show();


                            //if (data.flat.hasOwnProperty('type') && data.flat.type === 'zinc'){
                            //    $(".cover-zinc").show();
                            //}
                            if (data.flat.hasOwnProperty('savings_gas')){
                                $("input#flat_savings_gas").val(Math.round(data.flat.savings_gas).toLocaleString('{{ app()->getLocale() }}'));
                            }
                            if (data.flat.hasOwnProperty('savings_co2')){
                                $("input#flat_savings_co2").val(Math.round(data.flat.savings_co2).toLocaleString('{{ app()->getLocale() }}'));
                            }
                            if (data.flat.hasOwnProperty('savings_money')){
                                $("input#flat_savings_money").val(Math.round(data.flat.savings_money).toLocaleString('{{ app()->getLocale() }}'));
                            }
                            if (data.flat.hasOwnProperty('cost_indication')){
                                $("input#flat_cost_indication").val(Math.round(data.flat.cost_indication).toLocaleString('{{ app()->getLocale() }}'));
                            }
                            if (data.flat.hasOwnProperty('interest_comparable')){
                                $("input#flat_interest_comparable").val(data.flat.interest_comparable);
                            }
                            if (data.flat.hasOwnProperty('replace')){
                                if (data.flat.replace.hasOwnProperty('year')){
                                    $("input#flat_replace_year").val(data.flat.replace.year);
                                }
                                if (data.flat.replace.hasOwnProperty('costs')){
                                    $("input#flat_replace_cost").val(Math.round(data.flat.replace.costs).toLocaleString('{{ app()->getLocale() }}'));
                                }
                            }
                        }
                        else {

                            $(".flat-roof").hide();
                        }

                        $(".cover-tiles").hide();
                        if (data.hasOwnProperty('pitched')){


                            $(".pitched-roof").show();
                            if (data.pitched.hasOwnProperty('type')){

                                if(data.pitched.type === 'tiles'){
                                    $(".cover-tiles").show();
                                    $(".pitched-roof .cover-bitumen").hide();
                                }
                                if (data.pitched.type === 'bitumen'){
                                    $(".pitched-roof .cover-bitumen").show();
                                }
                            }
                            if (data.pitched.hasOwnProperty('savings_gas')){
                                $("input#pitched_savings_gas").val(Math.round(data.pitched.savings_gas).toLocaleString('{{ app()->getLocale() }}'));
                            }
                            if (data.pitched.hasOwnProperty('savings_co2')){
                                $("input#pitched_savings_co2").val(Math.round(data.pitched.savings_co2).toLocaleString('{{ app()->getLocale() }}'));
                            }
                            if (data.pitched.hasOwnProperty('savings_money')){
                                $("input#pitched_savings_money").val(Math.round(data.pitched.savings_money).toLocaleString('{{ app()->getLocale() }}'));
                            }
                            if (data.pitched.hasOwnProperty('cost_indication')){
                                $("input#pitched_cost_indication").val(Math.round(data.pitched.cost_indication).toLocaleString('{{ app()->getLocale() }}'));
                            }
                            if (data.pitched.hasOwnProperty('interest_comparable')){
                                $("input#pitched_interest_comparable").val(data.pitched.interest_comparable);
                            }
                            if (data.pitched.hasOwnProperty('replace')){
                                if (data.pitched.replace.hasOwnProperty('year')){
                                    $("input#pitched_replace_year").val(data.pitched.replace.year);
                                }
                                if (data.pitched.replace.hasOwnProperty('costs')){
                                    $("input#pitched_replace_cost").val(Math.round(data.pitched.replace.costs).toLocaleString('{{ app()->getLocale() }}'));
                                }
                            }
                        }
                        else {
                            $(".pitched-roof").hide();
                        }

                        @if(App::environment('local'))
                        console.log(data);
                        @endif
                    }
                });
            }

            $('form').find('*').filter(':input:visible:first').trigger('change');

        });


        $('input[name*=roof_surface]').on('change', function () {
            var insulationRoofSurface = $(this).parent().parent().parent().next().find('input');
            if (insulationRoofSurface.length > 0) {
                if ($(insulationRoofSurface).val().length == 0 || $(insulationRoofSurface).val() == "0,0" || $(insulationRoofSurface).val() == "0.00") {
                    $(insulationRoofSurface).val($(this).val())
                }
            }
        });


        $('select[name^=interest]').on('change', function () {
            $('select[name*=element_value_id]').trigger('change');
        });
        $('select[name*=element_value_id]').on('change', function () {
            var interestedCalculateValue = $('#interest_element_{{$roofInsulation->id}} option:selected').data('calculate-value');
            var elementCalculateValue = $(this).find(':selected').data('calculate-value');

            if ((elementCalculateValue == 3 || elementCalculateValue == 4) && interestedCalculateValue <= 2) {
                if ($(this).attr('name').includes('flat')) {
                    $('.flat-hideable').hide();
                    $('#flat-info-alert').find('.alert').removeClass('hide');
                } else if($(this).attr('name').includes('pitched')) {
                    $('.pitched-hideable').hide();
                    $('#pitched-info-alert').find('.alert').removeClass('hide');
                }
            } else {
                if ($(this).attr('name').includes('flat')) {
                    $('.flat-hideable').show();
                    $('#flat-info-alert').find('.alert').addClass('hide');
                } else if($(this).attr('name').includes('pitched')) {
                    $('.pitched-hideable').show();
                    $('#pitched-info-alert').find('.alert').addClass('hide');
                }
            }

        })


    </script>
@endpush