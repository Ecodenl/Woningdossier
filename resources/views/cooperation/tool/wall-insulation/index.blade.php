@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.wall-insulation.intro.title'))

@section('step_content')
    <form class="form-horizontal" method="POST" action="{{ route('cooperation.tool.wall-insulation.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}

        @include('cooperation.tool.includes.interested', ['type' => 'element'])
        <div id="intro">

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space{{ $errors->has('house_has_insulation') ? ' has-error' : '' }}">

                        @if(isset($building->buildingFeatures->build_year))
                        <label for="house_has_insulation" class=" control-label">
                            @lang('woningdossier.cooperation.tool.wall-insulation.intro.build-year', ['year' => $building->buildingFeatures->build_year])
                            @if($building->buildingFeatures->build_year >= 1985)
                                @lang('woningdossier.cooperation.tool.wall-insulation.intro.build-year-post-1985')
                            @elseif($building->buildingFeatures->build_year >= 1930)
                                @lang('woningdossier.cooperation.tool.wall-insulation.intro.build-year-post-1930')
                            @else
                                @lang('woningdossier.cooperation.tool.wall-insulation.intro.build-year-pre-1930')
                            @endif
                        </label>
                        @endif

                        <label for="element_{{ $facadeInsulation->element->id }}" class="control-label"><i data-toggle="collapse" data-target="#house-insulation-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.wall-insulation.intro.filled-insulation')</label>

                        <select id="element_{{ $facadeInsulation->element->id }}" class="form-control" name="element[{{ $facadeInsulation->element->id }}]">
                            @foreach($facadeInsulation->element->values()->orderBy('order')->get() as $elementValue)
                                <option
                                        @if(old('element.' . $facadeInsulation->element->id . '') && $elementValue->id == old('element.' . $facadeInsulation->element->id . ''))
                                        selected="selected"
                                        @elseif(isset($facadeInsulation->element_value_id) && $elementValue->id == $facadeInsulation->element_value_id)
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
                            I would like to have some helpful information right here!
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space{{ $errors->has('cavity_wall') ? ' has-error' : '' }}">
                        <label for="cavity_wall" class=" control-label"><i data-toggle="collapse" data-target="#cavity-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.wall-insulation.intro.has-cavity-wall') </label><span> *</span>

                        <label class="radio-inline">
                            <input type="radio" name="cavity_wall" @if(old('cavity_wall') == "1") checked @elseif(isset($buildingFeature) && $buildingFeature->cavity_wall == "1") checked @endif  value="1">@lang('woningdossier.cooperation.radiobutton.yes')
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="cavity_wall" @if(old('cavity_wall') == "2") checked @elseif(isset($buildingFeature) && $buildingFeature->cavity_wall == "2") checked @endif value="2">@lang('woningdossier.cooperation.radiobutton.no')
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="cavity_wall" @if(old('cavity_wall') == "0") checked @elseif(isset($buildingFeature) && $buildingFeature->cavity_wall == "0") checked @endif value="0">@lang('woningdossier.cooperation.radiobutton.unknown')
                        </label>
                        <br>

                        <div id="cavity-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            I would like to have some helpful information right here!
                        </div>

                        @if ($errors->has('cavity_wall'))
                            <span class="help-block">
                            <strong>{{ $errors->first('cavity_wall') }}</strong>
                        </span>
                        @endif

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space{{ $errors->has('facade_plastered_painted') ? ' has-error' : '' }}">

                        <label for="facade_plastered_painted" class=" control-label"><i data-toggle="collapse" data-target="#wall-painted" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.wall-insulation.intro.is-facade-plastered-painted')</label> <span> *</span>

                        <label class="radio-inline">
                            <input class="is-painted" @if(old('facade_plastered_painted') == "1") checked @elseif(isset($buildingFeature) && $buildingFeature->facade_plastered_painted == "1") checked @endif type="radio" name="facade_plastered_painted" value="1">@lang('woningdossier.cooperation.radiobutton.yes')
                        </label>
                        <label class="radio-inline">
                            <input @if(old('facade_plastered_painted') == "2") checked @elseif(isset($buildingFeature) && $buildingFeature->facade_plastered_painted == "2") checked @endif type="radio" name="facade_plastered_painted" value="2">@lang('woningdossier.cooperation.radiobutton.no')
                        </label>
                        <label class="radio-inline">
                            <input class="is-painted" @if(old('facade_plastered_painted') == "3") checked @elseif(isset($buildingFeature) && $buildingFeature->facade_plastered_painted == "3") checked @endif type="radio" name="facade_plastered_painted" value="3">@lang('woningdossier.cooperation.radiobutton.mostly')
                        </label>
                        <br>

                        <div id="wall-painted" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            I would like to have some helpful information right here!
                        </div>

                        @if ($errors->has('facade_plastered_painted'))
                            <span class="help-block">
                                <strong>{{ $errors->first('facade_plastered_painted') }}</strong>
                            </span>
                        @endif


                    </div>

                </div>

                <div id="painted-options" style="display: none;">
                    <div class="col-sm-6">
                        <div class="form-group add-space{{ $errors->has('facade_plastered_surface_id') ? ' has-error' : '' }}">
                            <label for="facade_plastered_surface_id" class=" control-label"><i data-toggle="collapse" data-target="#facade-painted-surface" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.wall-insulation.intro.surface-paintwork') </label>

                            <select id="facade_plastered_surface_id" class="form-control" name="facade_plastered_surface_id">
                                @foreach($facadePlasteredSurfaces as $facadePlasteredSurface)
                                    <option @if(old('facade_plastered_surface_id') == $facadePlasteredSurface->id) selected @elseif(isset($buildingFeature) && $buildingFeature->facade_plastered_surface_id == $facadePlasteredSurface->id ) selected @endif value="{{ $facadePlasteredSurface->id }}">{{ $facadePlasteredSurface->name }}</option>
                                @endforeach
                            </select>

                            <div id="facade-painted-surface" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
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
                            <label for="facade_damaged_paintwork_id" class=" control-label"><i data-toggle="collapse" data-target="#damage-paintwork-surface" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.wall-insulation.intro.damage-paintwork')</label>

                            <select id="facade_damaged_paintwork_id" class="form-control" name="facade_damaged_paintwork_id">
                                @foreach($facadeDamages as $facadeDamage)
                                    <option @if(old('facade_damaged_paintwork_id') == $facadeDamage->id) selected @elseif(isset($buildingFeature) && $buildingFeature->facade_damaged_paintwork_id == $facadeDamage->id ) selected  @endif value="{{ $facadeDamage->id }}">{{ $facadeDamage->name }}</option>
                                @endforeach
                            </select>

                            <div id="damage-paintwork-surface" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
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
        </div>

        <div id="options">
            <hr>
            <h4 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.wall-insulation.optional.title')</h4>

            <div id="wall-joints" class="row">
                <div class="col-sm-6">
                    <div class="form-group add-space{{ $errors->has('wall_joints') ? ' has-error' : '' }}">
                        <label for="wall_joints" class=" control-label"><i data-toggle="collapse" data-target="#wall-joints-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.wall-insulation.optional.flushing')</label>

                        <select id="wall_joints" class="form-control" name="wall_joints">
                            @foreach($surfaces as $surface)
                                <option @if(old('wall_joints') == $surface->id) selected @elseif(isset($buildingFeature) && $buildingFeature->wall_joints == $surface->id ) selected  @endif value="{{ $surface->id }}">{{ $surface->name }}</option>
                            @endforeach
                        </select>

                        <div id="wall-joints-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            I would like to have some helpful information right here!
                        </div>

                        @if ($errors->has('wall_joints'))
                            <span class="help-block">
                            <strong>{{ $errors->first('wall_joints') }}</strong>
                        </span>
                        @endif
                    </div>


                </div>

                <div class="col-sm-6">
                    <div class="form-group add-space{{ $errors->has('contaminated_wall_joints') ? ' has-error' : '' }}">
                        <label for="contaminated_wall_joints" class=" control-label"><i data-toggle="collapse" data-target="#wall-joints-surface" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.wall-insulation.optional.if-facade-dirty')</label>

                        <select id="contaminated_wall_joints" class="form-control" name="contaminated_wall_joints">
                            @foreach($surfaces as $surface)
                                <option @if(old('contaminated_wall_joints') == $surface->id) selected @elseif(isset($buildingFeature) && $buildingFeature->contaminated_wall_joints == $surface->id ) selected  @endif value="{{ $surface->id }}">{{ $surface->name }}</option>
                            @endforeach
                        </select>

                        <div id="wall-joints-surface" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            I would like to have some helpful information right here!
                        </div>

                        @if ($errors->has('contaminated_wall_joints'))
                            <span class="help-block">
                            <strong>{{ $errors->first('contaminated_wall_joints') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group add-space">
                        <label class="control-label">
                            <i data-toggle="collapse" data-target="#wall-surface-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.wall-insulation.optional.wall-surface')
                        </label>

                        <div class="input-group">
                            <input id="wall_surface" type="text" name="wall_surface" value="@if(old('wall_surface')){{ old('wall_surface') }}@elseif(isset($buildingFeature)){{ \App\Helpers\NumberFormatter::format($buildingFeature->wall_surface, 1) }}@endif" class="form-control" >
                            <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.square-meters')</span>
                        </div>

                        <div id="wall-surface-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            I would like to have some helpful information right here!
                        </div>
                        @if ($errors->has('insulation_wall_surface'))
                        <span class="help-block">
                            <strong>{{ $errors->first('insulation_wall_surface') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group add-space">
                        <label class="control-label">
                            <i data-toggle="collapse" data-target="#wall-surface-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.wall-insulation.optional.insulation-wall-surface')
                        </label>

                        <div class="input-group">
                            <input id="insulation_wall_surface" type="text" name="insulation_wall_surface" value="@if(old('insulation_wall_surface')){{ old('insulation_wall_surface') }}@elseif(isset($buildingFeature)){{ \App\Helpers\NumberFormatter::format($buildingFeature->insulation_wall_surface, 1) }}@endif" class="form-control" >
                            <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.square-meters')</span>
                        </div>

                        <div id="wall-surface-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            I would like to have some helpful information right here!
                        </div>
                        @if ($errors->has('insulation_wall_surface'))
                        <span class="help-block">
                            <strong>{{ $errors->first('insulation_wall_surface') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

            </div>

            <div class="row" id="advice-help">
                <div class="col-sm-12 col-md-8 col-md-offset-2">
                    <div class="alert alert-info" role="alert">
                        <p>@lang('woningdossier.cooperation.tool.wall-insulation.insulation-advice.text')</p>
                        <p id="insulation-advice"></p>
                    </div>
                </div>
            </div>
            <div class="row" id="cavity-wall-alert" style="display: none;">
                <div class="col-sm-12 col-md-8 col-md-offset-2">
                    <div class="alert alert-warning" role="alert">
                        <b><p>@lang('woningdossier.cooperation.tool.wall-insulation.alert.description')</p></b>
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
            <h6 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.wall-insulation.taking-into-account.sub-title')</h6>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.taking-into-account.repair-joint') <span id="repair_joint_year">(in 2018)</span></label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                            <input type="text" id="repair_joint" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.taking-into-account.clean-brickwork') <span id="clean_brickwork_year"></span></label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                            <input type="text" id="clean_brickwork" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.taking-into-account.impregnate-wall') <span id="impregnate_wall_year"></span></label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                            <input type="text" id="impregnate_wall" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.wall-insulation.taking-into-account.wall-painting') <span id="paint_wall_year"></span></label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                            <input type="text" id="paint_wall" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space{{ $errors->has('additional_info') ? ' has-error' : '' }}">
                        <label for="additional-info" class=" control-label"><i data-toggle="collapse" data-target="#additional-info-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('default.form.input.comment')        </label>

                        <textarea id="additional-info" class="form-control" name="additional_info">@if(old('additional_info')){{old('additional_info')}}@elseif(isset($buildingFeature)){{$buildingFeature->additional_info}}@endif</textarea>

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
                <div class="panel panel-primary">
                    <div class="panel-heading">@lang('default.buttons.download')</div>
                    <div class="panel-body">
                        <ol>
                            <li><a download="" href="{{asset('storage/hoomdossier-assets/Maatregelblad_Gevelisolatie.pdf')}}">{{ ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Gevelisolatie.pdf'))))) }}</a></li>
                            <li><a download="" href="{{asset('storage/hoomdossier-assets/Maatregelblad_Spouwisolatie.pdf')}}">{{ ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Spouwisolatie.pdf'))))) }}</a></li>
                            <?php $helpFile = "storage/hoomdossier-assets/Invul_hulp_Gevelisolatie.pdf"; ?>
                            <li><a download="" href="{{ asset($helpFile) }}">{{ ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset($helpFile))))) }}</a></li>
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
    </form>
@endsection

@push('js')
    <script>
        $(document).ready(function(){
            $(window).keydown(function(event){
                if(event.keyCode == 13) {
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
                              $('#taking-into-account').hide();
                              $('#indication-for-costs').hide();
                          } else  {
                              // hide the alert
                              $('#cavity-wall-alert').hide();

                              // Show the advice
                              $("#advice-help").show();
                              $('#taking-into-account').show();
                              $('#indication-for-costs').show();
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
                      @if(App::environment('local'))
                        console.log(data);
                      @endif
                  }
              })
            });
            // Trigger the change event so it will load the data
            $('form').find('*').filter(':input:visible:first').trigger('change');


        });

        $('#wall_surface').on('change', function () {
            if ($('#insulation_wall_surface').val().length == 0 || $('#insulation_wall_surface').val() == "0,0" || $('#insulation_wall_surface').val() == "0.00") {
                $('#insulation_wall_surface').val($('#wall_surface').val())
            }
        });

    </script>
@endpush

