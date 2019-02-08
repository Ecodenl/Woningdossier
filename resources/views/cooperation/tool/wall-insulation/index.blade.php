@extends('cooperation.tool.layout')

@section('step_title', \App\Helpers\Translation::translate('wall-insulation.title.title'))

@section('step_content')
    <form class="form-horizontal" method="POST" action="{{ route('cooperation.tool.wall-insulation.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}

        <div id="intro">
            @include('cooperation.tool.includes.interested', [
                'type' => 'element', 'buildingElements' => $buildingElements, 'buildingElement' => 'wall-insulation'
            ])
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space{{ $errors->has('house_has_insulation') ? ' has-error' : '' }}">

                        <label for="element_{{ $facadeInsulation->element->id }}" class="control-label"><i data-toggle="collapse" data-target="#house-insulation-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i> {{\App\Helpers\Translation::translate('wall-insulation.intro.filled-insulation.title')}}</label>

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $facadeInsulation->element->values()->orderBy('order')->get(), 'userInputValues' => $facadeInsulation->forMe()->get(), 'userInputColumn' => 'element_value_id'])
                            <select id="element_{{ $facadeInsulation->element->id }}" class="form-control" name="element[{{ $facadeInsulation->element->id }}]">
                                @foreach($facadeInsulation->element->values()->orderBy('order')->get() as $elementValue)
                                    <option data-calculate-value="{{$elementValue->calculate_value}}" @if(old('element.' . $facadeInsulation->element->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingElements()->where('element_id', $facadeInsulation->element->id), 'element_value_id')) == $elementValue->id) selected="selected" @endif value="{{ $elementValue->id }}">{{ $elementValue->value }}</option>
                                @endforeach
                            </select>
                        @endcomponent

                        @if ($errors->has('house_has_insulation'))
                            <span class="help-block">
                                <strong>{{ $errors->first('house_has_insulation') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="form-group add-space">
                        <div id="house-insulation-info" class="collapse alert alert-info remove-collapse-space">
                            {{\App\Helpers\Translation::translate('wall-insulation.intro.filled-insulation.help')}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('cooperation.tool.includes.savings-alert', ['buildingElement' => 'wall-insulation'])
        <div class="hideable">

            @if(isset($building->buildingFeatures->build_year))
                <div class="row">
                    <div class="col-sm-12">
                        <label for="house_has_insulation" class=" control-label">
                            {{\App\Helpers\Translation::translate('wall-insulation.intro.build-year.title', ['year' => $building->buildingFeatures->build_year]) }}
                            @if($building->buildingFeatures->build_year >= 1985)
                                {{\App\Helpers\Translation::translate('wall-insulation.intro.build-year-post-1985.title')}}
                            @elseif($building->buildingFeatures->build_year >= 1930)
                                {{\App\Helpers\Translation::translate('wall-insulation.intro.build-year-post-1930.title')}}
                            @else
                                {{\App\Helpers\Translation::translate('wall-insulation.intro.build-year-pre-1930.title')}}
                            @endif
                        </label>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space{{ $errors->has('cavity_wall') ? ' has-error' : '' }}">

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'radio',
                        'inputValues' => [
                            1 => \App\Helpers\Translation::translate('general.options.radio.yes.title'),
                            2 => \App\Helpers\Translation::translate('general.options.radio.no.title'),
                            0 => \App\Helpers\Translation::translate('general.options.radio.unknown.title'),
                        ],
                        'userInputValues' => $buildingFeaturesForMe, 'userInputColumn' => 'cavity_wall'])
                            <label for="cavity_wall" class=" control-label"><i data-toggle="collapse" data-target="#cavity-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>{{\App\Helpers\Translation::translate('wall-insulation.intro.has-cavity-wall.title')}} </label><span> *</span>
                            <label class="radio-inline">
                                    <input type="radio" name="cavity_wall" @if(old('cavity_wall', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'cavity_wall')) == 1) checked @endif value="1">{{\App\Helpers\Translation::translate('general.options.radio.yes.title') }}
                                {{--<input type="radio" name="cavity_wall" @if(old('cavity_wall') == "1") checked @elseif(isset($buildingFeature) && $buildingFeature->cavity_wall == "1") checked @endif  value="1">@lang('woningdossier.cooperation.radiobutton.yes')--}}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="cavity_wall" @if(old('cavity_wall', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'cavity_wall')) == 2) checked @endif value="2">{{\App\Helpers\Translation::translate('general.options.radio.no.title') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="cavity_wall" @if(old('cavity_wall', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'cavity_wall')) == 0) checked @endif value="0">{{\App\Helpers\Translation::translate('general.options.radio.unknown.title') }}
                            </label>
                        @endcomponent
                        <br>

                        <div id="cavity-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            {{\App\Helpers\Translation::translate('wall-insulation.intro.has-cavity-wall.help')}}
                        </div>

                        @if ($errors->has('cavity_wall'))
                            <span class="help-block">
                            <strong>{{ $errors->first('cavity_wall') }}</strong>
                        </span>
                        @endif

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group add-space{{ $errors->has('facade_plastered_painted') ? ' has-error' : '' }}">

                    @component('cooperation.tool.components.input-group',
                        ['inputType' => 'radio',
                        'inputValues' => [
                        1 => \App\Helpers\Translation::translate('general.options.radio.yes.title'),
                        2 => \App\Helpers\Translation::translate('general.options.radio.no.title'),
                        3 => \App\Helpers\Translation::translate('general.options.radio.unknown.title'),
                        ],
                        'userInputValues' => $buildingFeaturesForMe, 'userInputColumn' => 'facade_plastered_painted'])

                    <label for="facade_plastered_painted" class=" control-label"><i data-toggle="collapse" data-target="#wall-painted" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>{{\App\Helpers\Translation::translate('wall-insulation.intro.is-facade-plastered-painted.title')}} </label> <span> *</span>

                    <label class="radio-inline">
                        <input class="is-painted" @if(old('facade_plastered_painted', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'facade_plastered_painted')) == 1) checked @endif type="radio" name="facade_plastered_painted" value="1">{{ \App\Helpers\Translation::translate('general.options.radio.yes.title') }}
                    </label>
                    <label class="radio-inline">
                        <input @if(old('facade_plastered_painted', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'facade_plastered_painted')) == 2) checked @endif type="radio" name="facade_plastered_painted" value="2">{{ \App\Helpers\Translation::translate('general.options.radio.no.title') }}
                    </label>
                    <label class="radio-inline">
                        <input class="is-painted" @if(old('facade_plastered_painted', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'facade_plastered_painted')) == 3) checked @endif type="radio" name="facade_plastered_painted" value="3">{{ \App\Helpers\Translation::translate('general.options.radio.unknown.title') }}
                    </label>
                    @endcomponent
                    <br>

                    <div id="wall-painted" class="collapse alert alert-info remove-collapse-space alert-top-space">
                        {{\App\Helpers\Translation::translate('wall-insulation.intro.is-facade-plastered-painted.help')}}
                    </div>

                    @if ($errors->has('facade_plastered_painted'))
                        <span class="help-block">
                            <strong>{{ $errors->first('facade_plastered_painted') }}</strong>
                        </span>
                    @endif


                </div>
            </div>
        </div>

        <div class="row">
            <div id="painted-options" style="display: none;">
                <div class="col-sm-6">
                    <div class="form-group add-space{{ $errors->has('facade_plastered_surface_id') ? ' has-error' : '' }}">
                        <label for="facade_plastered_surface_id" class=" control-label"><i data-toggle="collapse" data-target="#facade-painted-surface" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>{{\App\Helpers\Translation::translate('wall-insulation.intro.surface-paintwork.title')}} </label>
                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'select', 'inputValues' => $facadePlasteredSurfaces, 'userInputValues' => $buildingFeaturesForMe, 'userInputColumn' => 'facade_plastered_surface_id'])
                        <select id="facade_plastered_surface_id" class="form-control" name="facade_plastered_surface_id">
                            @foreach($facadePlasteredSurfaces as $facadePlasteredSurface)<option @if(old('facade_plastered_surface_id', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'facade_plastered_surface_id'))  == $facadePlasteredSurface->id) selected="selected" @endif value="{{ $facadePlasteredSurface->id }}">{{ $facadePlasteredSurface->name }}</option>
                                {{--<option @if(old('facade_plastered_surface_id') == $facadePlasteredSurface->id) selected @elseif(isset($buildingFeature) && $buildingFeature->facade_plastered_surface_id == $facadePlasteredSurface->id ) selected @endif value="{{ $facadePlasteredSurface->id }}">{{ $facadePlasteredSurface->name }}</option>--}}
                            @endforeach
                        </select>@endcomponent

                        <div id="facade-painted-surface" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            {{\App\Helpers\Translation::translate('wall-insulation.intro.surface-paintwork.help')}}
                        </div>
                        <div id="facade-painted-surface" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            {{\App\Helpers\Translation::translate('wall-insulation.intro.surface-paintwork.help')}}
                        </div>

                    </div>

                    @if ($errors->has('facade_plastered_surface_id'))
                        <span class="help-block">
                            <strong>{{ $errors->first('facade_plastered_surface_id') }}</strong>
                        </span>
                    @endif

                </div>

                <div class="col-sm-6">
                    <div class="form-group add-space{{ $errors->has('facade_damaged_paintwork_id') ? ' has-error' : '' }}">
                        <label for="facade_damaged_paintwork_id" class=" control-label">
                                <i data-toggle="collapse" data-target="#damage-paintwork-surface" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                {{\App\Helpers\Translation::translate('wall-insulation.intro.damage-paintwork.title')}} </label>

                        @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $facadeDamages, 'userInputValues' => $buildingFeaturesForMe ,'userInputColumn' => 'facade_damaged_paintwork_id'])
                            <select id="facade_damaged_paintwork_id" class="form-control" name="facade_damaged_paintwork_id">
                            @foreach($facadeDamages as $facadeDamage)
                                <option @if(old('facade_damaged_paintwork_id', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'facade_damaged_paintwork_id'))  == $facadeDamage->id) selected="selected" @endif value="{{ $facadeDamage->id }}">{{ $facadeDamage->name }}</option>
                                        {{--<option @if(old('facade_damaged_paintwork_id') == $facadeDamage->id) selected @elseif(isset($buildingFeature) && $buildingFeature->facade_damaged_paintwork_id == $facadeDamage->id ) selected  @endif value="{{ $facadeDamage->id }}">{{ $facadeDamage->name }}</option>--}}
                            @endforeach
                            </select>
                        @endcomponent

                        <div id="damage-paintwork-surface" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            {{\App\Helpers\Translation::translate('wall-insulation.intro.damage-paintwork.help')}}
                        </div>

                    </div>

                    @if ($errors->has('facade_damaged_paintwork_id'))
                        <span class="help-block">
                            <strong>{{ $errors->first('facade_damaged_paintwork_id') }}</strong>
                        </span>
                    @endif

                </div>
            </div>
        </div>

            <div id="options">
                <hr>
                @include('cooperation.layouts.section-title', ['translationKey' => 'wall-insulation.optional.title'])

                <div id="wall-joints" class="row">
                    <div class="col-sm-6">
                        <div class="form-group add-space{{ $errors->has('wall_joints') ? ' has-error' : '' }}">
                            <label for="wall_joints" class=" control-label"><i data-toggle="collapse" data-target="#wall-joints-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>{{\App\Helpers\Translation::translate('wall-insulation.optional.flushing.title')}} </label>

                            @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $surfaces, 'userInputValues' => $buildingFeaturesForMe ,'userInputColumn' => 'wall_joints'])<select id="wall_joints" class="form-control" name="wall_joints">
                                @foreach($surfaces as $surface)
                                    <option @if(old('wall_joints', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'wall_joints'))  == $surface->id) selected="selected" @endif value="{{ $surface->id }}">{{ $surface->name }}</option>
                                    {{--<option @if(old('wall_joints') == $surface->id) selected @elseif(isset($buildingFeature) && $buildingFeature->wall_joints == $surface->id ) selected  @endif value="{{ $surface->id }}">{{ $surface->name }}</option>--}}
                                @endforeach
                            </select>@endcomponent

                            <div id="wall-joints-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                {{\App\Helpers\Translation::translate('wall-insulation.optional.flushing.help')}}
                            </div>

                            @if ($errors->has('wall_joints'))
                                <span class="help-block">
                                <strong>{{ $errors->first('wall_joints') }}</strong>
                            </span>
                            @endif
                        </div>


                    </div>

                    <div class="col-sm-6">
                        <div class="form-group add-space {{ $errors->has('contaminated_wall_joints') ? ' has-error' : '' }}">
                            <label for="contaminated_wall_joints" class=" control-label"><i data-toggle="collapse" data-target="#wall-joints-surface" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>{{\App\Helpers\Translation::translate('wall-insulation.optional.is-facade-dirty.title')}} </label>

                            @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $surfaces, 'userInputValues' => $buildingFeaturesForMe ,'userInputColumn' => 'contaminated_wall_joints'])<select id="contaminated_wall_joints" class="form-control" name="contaminated_wall_joints">
                                @foreach($surfaces as $surface)
                                    <option @if(old('contaminated_wall_joints') == $surface->id) selected @elseif(isset($buildingFeature) && $buildingFeature->contaminated_wall_joints == $surface->id ) selected  @endif value="{{ $surface->id }}">{{ $surface->name }}</option>
                                @endforeach
                            </select>@endcomponent

                            <div id="wall-joints-surface" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                {{\App\Helpers\Translation::translate('wall-insulation.optional.is-facade-dirty.help')}}
                            </div>

                            @if ($errors->has('contaminated_wall_joints'))
                                <span class="help-block">
                                <strong>{{ $errors->first('contaminated_wall_joints') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                </div>

            <div class="hideable">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group add-space @if ($errors->has('wall_surface')) has-error @endif">
                            <label class="control-label">
                                <i data-toggle="collapse" data-target="#wall-surface-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                {{\App\Helpers\Translation::translate('wall-insulation.optional.facade-surface.title')}}
                            </label>

                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'input', 'userInputValues' => $buildingFeaturesForMe ,'userInputColumn' => 'wall_surface', 'needsFormat' => true])
                                <input id="wall_surface" type="text" name="wall_surface" value="{{ \App\Helpers\NumberFormatter::format(old('wall_surface', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'wall_surface')),1) }}" class="form-control" >
                                <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.square-meters.title')}}</span>
                            @endcomponent

                            <div id="wall-surface-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                {{\App\Helpers\Translation::translate('wall-insulation.optional.facade-surface.help')}}
                            </div>
                            @if ($errors->has('wall_surface'))
                            <span class="help-block">
                                <strong>{{ $errors->first('wall_surface') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group add-space @if ($errors->has('insulation_wall_surface')) has-error @endif">
                            <label class="control-label">
                                <i data-toggle="collapse" data-target="#wall-surface-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                {{\App\Helpers\Translation::translate('wall-insulation.optional.insulated-surface.title')}}
                            </label>

                            @component('cooperation.tool.components.input-group',
                        ['inputType' => 'input', 'userInputValues' => $buildingFeaturesForMe ,'userInputColumn' => 'insulation_wall_surface', 'needsFormat' => true])
                                <input id="insulation_wall_surface" type="text" name="insulation_wall_surface" value="{{ \App\Helpers\NumberFormatter::format(old('insulation_wall_surface', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'insulation_wall_surface')),1) }}" class="form-control" >
                                <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.square-meters.title')}}</span>
                            @endcomponent

                            <div id="wall-surface-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                {{\App\Helpers\Translation::translate('wall-insulation.optional.insulated-surface.help')}}
                            </div>
                            @if ($errors->has('insulation_wall_surface'))
                            <span class="help-block">
                                <strong>{{ $errors->first('insulation_wall_surface') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="hideable">
                <div class="row" id="advice-help">
                    <div class="col-sm-12 col-md-8 col-md-offset-2">
                        <div class="alert alert-info" role="alert">
                            <p>{{\App\Helpers\Translation::translate('wall-insulation.insulation-advice.text.title')}}</p>
                            <p id="insulation-advice"></p>
                        </div>
                    </div>
                </div>
                <div class="row" id="cavity-wall-alert" style="display: none;">
                    <div class="col-sm-12 col-md-8 col-md-offset-2">
                        <div class="alert alert-warning" role="alert">
                            <p><strong>@lang('woningdossier.cooperation.tool.wall-insulation.alert.description')</strong></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hideable">
                <div id="indication-for-costs">
                    <hr>
                    @include('cooperation.layouts.section-title', [
                            'translationKey' => 'wall-insulation.indication-for-costs.title',
                            'infoAlertId' => 'indication-for-costs-info'
                        ])

                    <div id="costs" class="row">
                        <div class="col-sm-4">
                            @include('cooperation.layouts.indication-for-costs.gas', ['step' => $currentStep->slug])
                        </div>
                        <div class="col-sm-4">
                            @include('cooperation.layouts.indication-for-costs.co2', ['step' => $currentStep->slug])
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
            </div>
            <div id="taking-into-account">
                <hr>
                @include('cooperation.layouts.section-title', ['translationKey' => 'wall-insulation.taking-into-account.title'])
                <h6 style="margin-left: -5px;">{{\App\Helpers\Translation::translate('wall-insulation.taking-into-account.sub-title.title')}}</h6>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group add-space">
                            <label class="control-label">
                                <i data-toggle="collapse" data-target="#repair-join-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                {{\App\Helpers\Translation::translate('wall-insulation.taking-into-account.repair-joint.title')}} <span id="repair_joint_year">(in 2018)</span></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                                <input type="text" id="repair_joint" class="form-control disabled" disabled="" value="0">
                            </div>
                            <div id="repair-join-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                {{\App\Helpers\Translation::translate('wall-insulation.taking-into-account.repair-joint.help')}}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group add-space">
                            <label class="control-label">
                                <i data-toggle="collapse" data-target="#clean-brickwork-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                {{\App\Helpers\Translation::translate('wall-insulation.taking-into-account.clean-brickwork.title')}} <span id="clean_brickwork_year"></span></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                                <input type="text" id="clean_brickwork" class="form-control disabled" disabled="" value="0">
                            </div>
                            <div id="clean-brickwork-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                {{\App\Helpers\Translation::translate('wall-insulation.taking-into-account.clean-brickwork.help')}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group add-space">
                            <label class="control-label">
                                <i data-toggle="collapse" data-target="#impregnate-wall-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                {{\App\Helpers\Translation::translate('wall-insulation.taking-into-account.impregnate-wall.title')}}
                                <span id="impregnate_wall_year"></span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                                <input type="text" id="impregnate_wall" class="form-control disabled" disabled="" value="0">
                            </div>
                            <div id="impregnate-wall-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                {{\App\Helpers\Translation::translate('wall-insulation.taking-into-account.impregnate-wall.help')}}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group add-space">
                            <label class="control-label">
                                <i data-toggle="collapse" data-target="#paint-wall-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                {{\App\Helpers\Translation::translate('wall-insulation.taking-into-account.wall-painting.title')}} <span id="paint_wall_year"></span></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                                <input type="text" id="paint_wall" class="form-control disabled" disabled="" value="0">
                            </div>
                            <div id="paint-wall-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                {{\App\Helpers\Translation::translate('wall-insulation.taking-into-account.wall-painting.help')}}
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group add-space{{ $errors->has('additional_info') ? ' has-error' : '' }}">
                            <label for="additional-info" class=" control-label"><i data-toggle="collapse" data-target="#additional-info-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>{{\App\Helpers\Translation::translate('general.specific-situation.title')}}</label>

                            <textarea id="additional-info" class="form-control" name="additional_info">@if(old('additional_info')){{old('additional_info')}}@elseif(isset($buildingFeature)){{$buildingFeature->additional_info}}@endif</textarea>

                            <div id="additional-info-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                {{\App\Helpers\Translation::translate('general.specific-situation.help')}}
                            </div>

                            @if ($errors->has('additional_info'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('additional_info') }}</strong>
                                </span>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
            @include('cooperation.tool.includes.comment', [
               'collection' => $buildingFeaturesForMe,
               'commentColumn' => 'additional_info',
               'translation' => [
                   'title' => 'general.specific-situation.title',
                   'help' => 'general.specific-situation.help'
               ]
           ])

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">@lang('default.buttons.download')</div>
                        <div class="panel-body">
                            <ol>
                                <li><a download="" href="{{asset('storage/hoomdossier-assets/Maatregelblad_Gevelisolatie.pdf')}}">{{ ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Gevelisolatie.pdf'))))) }}</a></li>
                                <li><a download="" href="{{asset('storage/hoomdossier-assets/Maatregelblad_Spouwisolatie.pdf')}}">{{ ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Spouwisolatie.pdf'))))) }}</a></li>
                            </ol>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group add-space">
                        <div class="">
                            <a class="btn btn-success pull-left" href="{{ route('cooperation.tool.general-data.index', ['cooperation' => $cooperation]) }}">@lang('default.buttons.prev')</a>
                            <button type="submit" class="btn btn-primary pull-right">
                                @lang('default.buttons.next')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('js')
    <script>
        $(document).ready(function(){
            $(window).keydown(function(event){
                if(event.keyCode === 13) {
                    event.preventDefault();
                    return false;
                }
            });

           $("select, input[type=radio], input[type=text]").change(function(){
               if ($('.is-painted').is(':checked')) {
                   $('#painted-options').show();
               } else {
                   $('#painted-options').hide();
               }

               var form = $(this).closest("form").serialize();
              $.ajax({
                  type: "POST",
                  url: '{{ route('cooperation.tool.wall-insulation.calculate', [ 'cooperation' => $cooperation ]) }}',
                  data: form,
                  success: function(data){
                      if (data.hasOwnProperty('insulation_advice')){
                          $("#insulation-advice").html("<strong>" + data.insulation_advice + "</strong>");

                          // If the advice is spouwmuurisolatie and the walls are painted give them a alert
                          if ((data.insulation_advice == "Spouwmuurisolatie") && ($('.is-painted').is(':checked') == true)) {
                              // Show the alert
                              $('#cavity-wall-alert').show();

                              // Hide the advice
                              $("#advice-help").hide();
                              // Hide the indications and measures
                              // $('#taking-into-account').hide();
                              // $('#indication-for-costs').hide();
                          } else  {
                              // hide the alert
                              $('#cavity-wall-alert').hide();

                              // Show the advice
                              $("#advice-help").show();
                              // $('#taking-into-account').show();
                              // $('#indication-for-costs').show();
                          }

                      }
                      else {
                          $("#insulation-advice").html("");
                      }
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
                      if (data.hasOwnProperty('repair_joint')){
                          $("input#repair_joint").val(Math.round(data.repair_joint.costs));
                          var contentYear = "";
                          if (data.repair_joint.year > 0){
                              contentYear = "(in " + data.repair_joint.year + ")";
                          }
                          $("span#repair_joint_year").html(contentYear);
                      }
                      if (data.hasOwnProperty('clean_brickwork')){
                          $("input#clean_brickwork").val(Math.round(data.clean_brickwork.costs));
                          var contentYear = "";
                          if (data.clean_brickwork.year > 0){
                              contentYear = "(in " + data.clean_brickwork.year + ")";
                          }
                          $("span#clean_brickwork_year").html(contentYear);
                      }
                      if (data.hasOwnProperty('impregnate_wall')){
                          $("input#impregnate_wall").val(Math.round(data.impregnate_wall.costs));
                          var contentYear = "";
                          if (data.impregnate_wall.year > 0){
                              contentYear = "(in " + data.impregnate_wall.year + ")";
                          }
                          $("span#impregnate_wall_year").html(contentYear);
                      }
                      if (data.hasOwnProperty('paint_wall')){
                          $("input#paint_wall").val(Math.round(data.paint_wall.costs));
                          var contentYear = "";
                          if (data.paint_wall.year > 0){
                              contentYear = "(in " + data.paint_wall.year + ")";
                          }
                          $("span#paint_wall_year").html(contentYear);

                      }

                      checkInterestAndCurrentInsulation();

                      @if(App::environment('local'))
                        console.log(data);
                      @endif
                  }
              })
            });
            // Trigger the change event so it will load the data
            $('.panel-body form').find('*').filter(':input:visible:first').trigger('change');


        });

        $('#wall_surface').on('change', function () {
            if ($('#insulation_wall_surface').val().length == 0 || $('#insulation_wall_surface').val() == "0,0" || $('#insulation_wall_surface').val() == "0.00") {
                $('#insulation_wall_surface').val($('#wall_surface').val())
            }
        });

        function checkInterestAndCurrentInsulation(){
            var elementCalculateValue = $('#element_{{$buildingElements->id}} option:selected').data('calculate-value');

            console.log(elementCalculateValue);
            if (elementCalculateValue >= 3) {
                console.log('hide');
                $('.hideable').hide();
                $('#wall-insulation-info-alert').find('.alert').removeClass('hide');
            } else {
                console.log('show');
                $('.hideable').show();
                $('#wall-insulation-info-alert').find('.alert').addClass('hide');
            }
        }

    </script>
@endpush

