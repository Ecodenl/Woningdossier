@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.wall-insulation.intro.title'))


@section('step_content')
    <form class="form-horizontal" method="POST" action="{{ route('cooperation.tool.wall-insulation.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        <div id="intro">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space{{ $errors->has('house_has_insulation') ? ' has-error' : '' }}">

                        @if(isset($building->buildingFeatures->build_year))
                        <label for="house_has_insulation" class=" control-label">
                            @lang('woningdossier.cooperation.tool.wall-insulation.intro.build-year', ['year' => $building->buildingFeatures->build_year])
                            @if($building->buildingFeatures->build_year > 1985)
                                @lang('woningdossier.cooperation.tool.wall-insulation.intro.build-year-post-1985')
                            @elseif($building->buildingFeatures->build_year > 1930)
                                @lang('woningdossier.cooperation.tool.wall-insulation.intro.build-year-post-1930')
                            @else
                                @lang('woningdossier.cooperation.tool.wall-insulation.intro.build-year-pre-1930')
                            @endif
                        </label>
                        @endif

                        <label for="element_{{ $houseInsulation->element->id }}" class="control-label"><i data-toggle="collapse" data-target="#house-insulation-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.wall-insulation.intro.filled-insulation')           </label>

                        <select id="element_{{ $houseInsulation->element->id }}" class="form-control" name="element[{{ $houseInsulation->element->id }}]">
                            @foreach($houseInsulation->element->values()->orderBy('order')->get() as $elementValue)
                                <option
                                        @if(old('element[' . $houseInsulation->element->id . ']') && $elementValue->id == old('element[' . $houseInsulation->element->id . ']'))
                                        selected="selected"
                                        @elseif(isset($houseInsulation->elementValue) && $houseInsulation->elementValue->id == $elementValue->id)
                                        selected="selected"
                                        @endif
                                value="{{ $elementValue->id }}">{{ $elementValue->value }}</option>
                            @endforeach
                        </select>

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
                            I would like to have some helpful information right here!
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space{{ $errors->has('house_has_cavity') ? ' has-error' : '' }}">
                        <label for="house_has_cavity" class=" control-label"><i data-toggle="collapse" data-target="#cavity-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.wall-insulation.intro.has-cavity-wall') </label>

                        <label class="radio-inline">
                            <input type="radio" name="house_has_cavity" value="1">@lang('woningdossier.cooperation.radiobutton.yes')
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="house_has_cavity" value="2">@lang('woningdossier.cooperation.radiobutton.no')
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="house_has_cavity" value="3">@lang('woningdossier.cooperation.radiobutton.unknown')
                        </label>
                        <br>

                        <div id="cavity-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            I would like to have some helpful information right here!
                        </div>

                        @if ($errors->has('house_has_cavity'))
                            <span class="help-block">
                            <strong>{{ $errors->first('house_has_cavity') }}</strong>
                        </span>
                        @endif

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space{{ $errors->has('is_facade_plastered_painted') ? ' has-error' : '' }}">

                        <label for="is_facade_plastered_painted" class=" control-label"><i data-toggle="collapse" data-target="#wall-painted" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.wall-insulation.intro.is-facade-plastered-painted') </label>

                        <label class="radio-inline">
                            <input id="is-painted" type="radio" name="is_facade_plastered_painted" value="1">@lang('woningdossier.cooperation.radiobutton.yes')
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="is_facade_plastered_painted" value="2">@lang('woningdossier.cooperation.radiobutton.no')
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="is_facade_plastered_painted" value="3">@lang('woningdossier.cooperation.radiobutton.mostly')
                        </label>
                        <br>

                        <div id="wall-painted" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            I would like to have some helpful information right here!
                        </div>

                        @if ($errors->has('is_facade_plastered_painted'))
                            <span class="help-block">
                                <strong>{{ $errors->first('is_facade_plastered_painted') }}</strong>
                            </span>
                        @endif


                    </div>

                </div>

                <div id="painted-options" style="display: none;">
                    <div class="col-sm-6">
                        <div class="form-group add-space{{ $errors->has('is_facade_plastered_painted_surface') ? ' has-error' : '' }}">
                            <label for="is_facade_plastered_painted_surface" class=" control-label"><i data-toggle="collapse" data-target="#facade-painted-surface" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.wall-insulation.intro.surface-paintwork') </label>

                            <select id="is_facade_plastered_painted_surface" class="form-control" name="is_facade_plastered_painted_surface">
                                @foreach($surfacePaintedWalls as $surfacePaintedWall)
                                    <option @if(old('is_facade_plastered_painted_surface') == $surfacePaintedWall->id) selected @endif value="{{$surfacePaintedWall->id }}">{{$surfacePaintedWall->name}}</option>
                                @endforeach
                            </select>

                            <div id="facade-painted-surface" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                        </div>

                        @if ($errors->has('is_facade_plastered_painted_surface'))
                            <span class="help-block">
                                <strong>{{ $errors->first('is_facade_plastered_painted_surface') }}</strong>
                            </span>
                        @endif

                    </div>

                    <div class="col-sm-6">
                        <div class="form-group add-space{{ $errors->has('damage_paintwork_surface') ? ' has-error' : '' }}">
                            <label for="damage_paintwork" class=" control-label"><i data-toggle="collapse" data-target="#damage-paintwork-surface" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.wall-insulation.intro.damage-paintwork') </label>

                            <select id="damage_paintwork" class="form-control" name="damage_paintwork">
                                @foreach($surfacePaintedWalls as $surfacePaintedWall)
                                    <option @if(old('damage_paintwork_surface') == $surfacePaintedWall->id) selected @endif value="{{$surfacePaintedWall->id }}">{{$surfacePaintedWall->name}}</option>
                                @endforeach
                            </select>

                            <div id="damage-paintwork-surface" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                        </div>

                        @if ($errors->has('is_facade_plastered_painted_surface'))
                            <span class="help-block">
                                <strong>{{ $errors->first('is_facade_plastered_painted_surface') }}</strong>
                            </span>
                        @endif

                    </div>
                </div>

            </div>
        </div>

        <div id="options">
            <hr>
            <h4 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.wall-insulation.optional.title')</h4>

            <div id="wall-joints" class="row">
                <div class="col-sm-6">
                    <div class="form-group add-space{{ $errors->has('is_facade_plastered_painted_surface') ? ' has-error' : '' }}">
                        <label for="wall_joints" class=" control-label"><i data-toggle="collapse" data-target="#wall-joints-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.wall-insulation.optional.flushing')           </label>

                        <select id="wall_joints" class="form-control" name="wall_joints">
                            @foreach($wallsNeedImpregnation as $wallNeedImpregnation)
                                <option @if(old('wall_joints') == $wallNeedImpregnation->id) selected @endif value="{{$wallNeedImpregnation->id }}">{{$wallNeedImpregnation->name}}</option>
                            @endforeach
                        </select>

                        <div id="wall-joints-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            I would like to have some helpful information right here!
                        </div>
                    </div>

                    @if ($errors->has('wall_joints'))
                        <span class="help-block">
                            <strong>{{ $errors->first('wall_joints') }}</strong>
                        </span>
                    @endif


                </div>

                <div class="col-sm-6">
                    <div class="form-group add-space{{ $errors->has('contaminated_wall_joints_surface') ? ' has-error' : '' }}">
                        <label for="contaminated_wall_joints" class=" control-label"><i data-toggle="collapse" data-target="#wall-joints-surface" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.wall-insulation.optional.if-facade-dirty') </label>

                        <select id="contaminated_wall_joints_surface" class="form-control" name="contaminated_wall_joints_surface">
                            @foreach($wallsNeedImpregnation as $wallNeedImpregnation)
                                <option @if(old('contaminated_wall_joints_surface') == $wallNeedImpregnation->id) selected @endif value="{{$wallNeedImpregnation->id }}">{{$wallNeedImpregnation->name}}</option>
                            @endforeach
                        </select>

                        <div id="wall-joints-surface" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            I would like to have some helpful information right here!
                        </div>

                    </div>

                    @if ($errors->has('contaminated_wall_joints_surface'))
                        <span class="help-block">
                            <strong>{{ $errors->first('contaminated_wall_joints_surface') }}</strong>
                        </span>
                    @endif

                </div>

            </div>

            <div id="facade-surface" class="row">
                <div class="col-sm-6">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.optional.house-with-same-situation')           </label>
                        <input type="text" class="form-control disabled" disabled="" value="WAARDE">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group add-space{{ $errors->has('is_facade_plastered_painted') ? ' has-error' : '' }}">
                        <label for="facade_surface" class="control-label"><i data-toggle="collapse" data-target="#facade-surface-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.wall-insulation.optional.not-right')           </label>
                        <input id="facade_surface" type="text" name="facade_surface" value="" class="form-control">

                        <div id="facade-surface-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            I would like to have some helpful information right here!
                        </div>

                    </div>

                    @if ($errors->has('is_facade_plastered_painted'))
                        <span class="help-block">
                            <strong>{{ $errors->first('is_facade_plastered_painted') }}</strong>
                        </span>
                    @endif

                </div>
            </div>

            <div id="best-solution" class="row">
                <div class="col-sm-6">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.optional.facade-best-insulation')           </label>
                        <input type="text" class="form-control disabled" disabled="" value="BEST SOLUTION">
                    </div>
                </div>
            </div>

        </div>

        <div id="indication-for-costs">
            <hr>
            <h4 style="margin-left: -5px">@lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.title')</h4>

            <div id="costs" class="row">
                <div class="col-sm-2">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.gas-savings')           </label>
                        <div class="input-group">
                            <span class="input-group-addon">m3 / @lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.year')</span>
                            <input type="text" class="form-control disabled" disabled="" value="114">
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.co2-savings') </label>
                        <div class="input-group">
                            <span class="input-group-addon">CO2 / @lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.year')</span>
                            <input type="text" class="form-control disabled" disabled="" value="215">
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.savings-in-euro') </label>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i> / @lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.year')</span>
                            <input type="text" class="form-control disabled" disabled="" value="63">
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.indicative-costs')           </label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                            <input type="text" class="form-control disabled" disabled="" value="653">
                        </div>
                    </div>

                </div>
                <div class="col-sm-2">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.comparable-rate')           </label>
                        <div class="input-group">
                            <span class="input-group-addon">% / @lang('woningdossier.cooperation.tool.wall-insulation.indication-for-costs.year')</span>
                            <input type="text" class="form-control disabled" disabled="" value="5,3">
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <div id="taking-into-account">
            <hr>
            <h4 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.wall-insulation.taking-into-account.title')</h4>
            <h6 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.wall-insulation.taking-into-account.sub-title')</h6>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.taking-into-account.repair-joint')           </label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                            <input type="text" class="form-control disabled" disabled="" value="114">
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.taking-into-account.clean-brickwork')           </label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                            <input type="text" class="form-control disabled" disabled="" value="114">
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.taking-into-account.impregnate-wall')           </label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                            <input type="text" class="form-control disabled" disabled="" value="114">
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.taking-into-account.wall-painting')           </label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                            <input type="text" class="form-control disabled" disabled="" value="114">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space{{ $errors->has('additional_info') ? ' has-error' : '' }}">
                        <label for="additional-info" class=" control-label"><i data-toggle="collapse" data-target="#additional-info-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.wall-insulation.taking-into-account.additional-info')           </label>

                        <textarea id="additional-info" class="form-control" name="additional-info"> {{old('additional_info')}} </textarea>

                        <div id="additional-info-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            I would like to have some helpful information right here!
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

        <div class="row">
            <div class="col-md-12">
                <hr>
                <div class="form-group add-space">
                    <div class="">
                        <a class="btn btn-success pull-left" href="{{route('cooperation.tool.general-data.index', ['cooperation' => $cooperation])}}">@lang('default.buttons.prev')</a>
                        <button type="submit" disabled class="disabled btn btn-primary pull-right">
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
        $( document ).change(function() {
            // check if the is painted button is yes
            if ($('#is-painted').is(':checked')) {
                $('#painted-options').show();
            } else {
                $('#painted-options').hide();
            }
        });
    </script>
@endpush

