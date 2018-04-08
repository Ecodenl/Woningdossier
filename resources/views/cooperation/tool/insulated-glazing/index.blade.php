@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.insulated-glazing.title'))


@section('step_content')

    <form class="form-horizontal" method="POST" action="{{ route('cooperation.tool.insulated-glazing.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        <div id="main-glass-questions">
            <div class="row">
                <div class="col-sm-12">
                    <h4 style="margin-left: -5px;">@lang('woningdossier.cooperation.tool.insulated-glazing.title')</h4>
                </div>
            </div>
            @foreach ($keys as $key)
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group add-space {{ $errors->has($key) ? ' has-error' : '' }}">
                            <label class=" control-label">
                                <i data-toggle="collapse" data-target="#{{$key}}-info"
                                   class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('woningdossier.cooperation.tool.insulated-glazing.'.$key.'.title')
                            </label>

                            <select id="{{ $key }}" class="form-control" name="{{ $key }}" >
                                @foreach($interests as $interest)
                                    <option @if($interest->id == old($key)) selected @endif value="{{ $interest->id }}">{{ $interest->name }}</option>
                                @endforeach
                            </select>

                            <select class="form-control" name="{{$key}}">
                                @foreach($interestedToExecuteMeasures as $interestedToExecuteMeasure)
                                    <option @if($interestedToExecuteMeasure->id == old($key)) selected
                                            @endif value="{{$interestedToExecuteMeasure->id}}">@lang($interestedToExecuteMeasure->name)</option>
                                @endforeach
                            </select>

                            <div id="{{$key}}-info"
                                 class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                            @if ($errors->has($key))
                                <span class="help-block">
                                    <strong>{{ $errors->first($key) }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class=" col-sm-3 ">
                        <div class="form-group add-space {{ $errors->has($key) ? ' has-error' : '' }}">
                            <label class=" control-label">
                                <i data-toggle="collapse" data-target="#{{$key}}-current-glass-info"
                                   class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('woningdossier.cooperation.tool.insulated-glazing.current-glass')
                            </label>

                            <select class="form-control" name="{{$key}}[current-glass]">
                                @foreach($insulatedGlazings as $insulateGlazing)
                                    <option @if($insulateGlazing->id == old($key.'current-glass')) selected
                                            @endif value="{{$insulateGlazing->id}}">@lang($insulateGlazing->name)</option>
                                @endforeach
                            </select>

                            <div id="{{$key}}-current-glass-info"
                                 class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                            @if ($errors->has($key.'current-glass'))
                                <span class="help-block">
                                    <strong>{{ $errors->first($key.'current-glass') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class=" col-sm-3 ">
                        <div class="form-group add-space {{ $errors->has($key) ? ' has-error' : '' }}">
                            <label class=" control-label">
                                <i data-toggle="collapse" data-target="#{{$key}}-heated-rooms-info"
                                   class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('woningdossier.cooperation.tool.insulated-glazing.heated-rooms')
                            </label>

                            <select class="form-control" name="{{$key}}[heated-rooms]">
                                @foreach($insulatedGlazings as $insulateGlazing)
                                    <option @if($insulateGlazing->id == old($key.'heated-rooms')) selected
                                            @endif value="{{$insulateGlazing->id}}">@lang($insulateGlazing->name)</option>
                                @endforeach
                            </select>

                            <div id="{{$key}}-heated-rooms-info"
                                 class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                            @if ($errors->has($key.'heated-rooms'))
                                <span class="help-block">
                                    <strong>{{ $errors->first($key['heated-rooms']) }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class=" col-sm-3 ">
                        <div class="form-group add-space {{ $errors->has($key) ? ' has-error' : '' }}">
                            <label class=" control-label">
                                <i data-toggle="collapse" data-target="#{{$key}}-m2-info"
                                   class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('woningdossier.cooperation.tool.insulated-glazing.m2')
                            </label>

                            <input type="text" name="{{$key}}[m2]" value="{{old($key.'m2')}}" class="form-control">

                            <div id="{{$key}}-m2-info"
                                 class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                            @if ($errors->has($key.'m2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first($key.'m2') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class=" col-sm-3 ">
                        <div class="form-group add-space {{ $errors->has($key) ? ' has-error' : '' }}">
                            <label class=" control-label">
                                <i data-toggle="collapse" data-target="#{{$key}}-total-windows-info"
                                   class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('woningdossier.cooperation.tool.insulated-glazing.total-windows')
                            </label>

                            <input type="text" name="{{$key}}[total-windows]" value="{{old($key.'total-windows')}}"
                                   class="form-control">
                            <div id="{{$key}}-total-windows-info"
                                 class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                            @if ($errors->has($key.'total-windows'))
                                <span class="help-block">
                                    <strong>{{ $errors->first($key.'total-windows') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <hr>
            @endforeach

        </div>

        <div id="remaining-questions">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space {{ $errors->has('window_surface') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#window-surface-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.insulated-glazing.windows-surface.comparable-houses')
                            @lang('woningdossier.cooperation.tool.insulated-glazing.windows-surface.not-right')
                        </label>

                        <input type="text" name="window_surface"  value="{{old('window_surface', 'WAARDE')}}" class="form-control">

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
                    <div class="form-group add-space {{ $errors->has('moving_parts_quality') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#moving-parts-quality-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.insulated-glazing.moving-parts-quality')
                        </label>

                        <select class="form-control" name="moving_parts_quality">
                            @foreach($insulateQualities as $insulateQuality)
                                <option @if($insulateQuality->id == old('moving_parts_quality')) selected @endif value="{{$insulateGlazing->id}}">@lang($insulateQuality->name)</option>
                            @endforeach
                        </select>

                        <div id="moving-parts-quality-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('moving_parts_quality'))
                            <span class="help-block">
                                <strong>{{ $errors->first('moving_parts_quality') }}</strong>
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
                    <h4 style="margin-left: -5px;" >@lang('woningdossier.cooperation.tool.insulated-glazing.paint-work.title')</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space {{ $errors->has('which_frames') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#which-frames-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.insulated-glazing.paint-work.which-frames')
                        </label>

                        <select class="form-control" name="which_frames">
                            @foreach($houseFrames as $houseFrame)
                                <option @if($houseFrame->id == old('which_frames')) selected @endif value="{{$houseFrame->id}}">{{$houseFrame->name}}</option>
                            @endforeach
                        </select>

                        <div id="which-frames-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('which_frames'))
                            <span class="help-block">
                            <strong>{{ $errors->first('which_frames') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space {{ $errors->has('woodelement') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#wood-element-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.insulated-glazing.paint-work.other-wood-elements.title')
                        </label>

                        <div id="wood-element-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('woodelement'))
                            <span class="help-block">
                                <strong>{{ $errors->first('woodelement') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group add-space {{ $errors->has('woodelement') ? ' has-error' : '' }}">
                        @foreach($woodElements as $woodElement)
                            <label for="" class="checkbox-inline">
                                <input type="checkbox" name="woodelement[{{str_slug(__($woodElement->translation_key))}}]">
                                @lang($woodElement->translation_key)
                            </label>

                            @if ($errors->has(str_slug(__($woodElement->translation_key))))
                                <span class="help-block">
                                    <strong>{{ $errors->first(str_slug(__($woodElement->translation_key))) }}</strong>
                                </span>
                            @endif

                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group add-space {{ $errors->has('last_paint_job') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#last-paint-job-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.insulated-glazing.paint-work.last-paintjob')
                        </label>

                        <input type="text" name="last_paint_job" class="form-control">

                        <div id="last-paint-job-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('last_paint_job'))
                            <span class="help-block">
                            <strong>{{ $errors->first('last_paint_job') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group add-space {{ $errors->has('paint_damage_visible') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#paint-damage-visible-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.insulated-glazing.paint-work.paint-damage-visible')
                        </label>

                        <select class="form-control" name="paint_damage_visible">
                            @foreach($damageToPaintWorks as $damageToPaintWork)
                                <option @if($damageToPaintWork->id == old('wood_root_visible')) selected @endif value="{{$damageToPaintWork->id}}">@lang($damageToPaintWork->name)</option>
                            @endforeach
                        </select>

                        <div id="paint-damage-visible-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('paint_damage_visible'))
                            <span class="help-block">
                            <strong>{{ $errors->first('paint_damage_visible') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space {{ $errors->has('wood_root_visible') ? ' has-error' : '' }}">
                        <label for="" class="control-label">
                            <i data-toggle="collapse" data-target="#wood-rot-visible-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.insulated-glazing.paint-work.wood-rot-visible')
                        </label>

                        <select class="form-control" name="wood_root_visible">
                            @foreach($damageToPaintWorks as $damageToPaintWork)
                                <option @if($damageToPaintWork->id == old('wood_root_visible')) selected @endif value="{{$damageToPaintWork->id}}">@lang($damageToPaintWork->name)</option>
                            @endforeach
                        </select>

                        <div id="wood-rot-visible-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('wood_root_visible'))
                            <span class="help-block">
                                <strong>{{ $errors->first('wood_root_visible') }}</strong>
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
                        <a class="btn btn-success pull-left" href="{{route('cooperation.tool.insulated-glazing.store', ['cooperation' => $cooperation])}}">@lang('default.buttons.prev')</a>
                        <button type="submit" class="disabled btn btn-primary pull-right">
                            @lang('default.buttons.next')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection