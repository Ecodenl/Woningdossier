@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.insulated-glazing.title'))


@section('step_content')

    <form class="form-horizontal" method="POST" action="{{ route('cooperation.tool.insulated-glazing.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        <div id="glass-in-lead-replacement">
            @foreach ($keys as $key)
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group add-space {{ $errors->has($key) ? ' has-error' : '' }}">
                            <label class=" control-label">
                                <i data-toggle="collapse" data-target="#{{$key}}-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('woningdossier.cooperation.tool.insulated-glazing.'.$key.'.title')
                            </label>

                            <select class="form-control" name="{{$key}}" >
                                @foreach($interestedToExecuteMeasures as $interestedToExecuteMeasure)
                                    <option @if($interestedToExecuteMeasure->id == old($key)) selected @endif value="{{$interestedToExecuteMeasure->id}}">@lang($interestedToExecuteMeasure->name)</option>
                                @endforeach
                            </select>

                            <div id="{{$key}}-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                            @if ($errors->has($key))
                                <span class="help-block">
                                    <strong>{{ $errors->first('example_building_type') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class=" col-sm-3 ">
                        <div class="form-group add-space {{ $errors->has($key) ? ' has-error' : '' }}">
                            <label class=" control-label">
                                <i data-toggle="collapse" data-target="#{{$key}}-current-glass-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('woningdossier.cooperation.tool.insulated-glazing.current-glass')
                            </label>

                            <select class="form-control" name="{{$key}}[current-glass]" >
                                @foreach($interestedToExecuteMeasures as $interestedToExecuteMeasure)
                                    <option @if($interestedToExecuteMeasure->id == old($key)) selected @endif value="{{$interestedToExecuteMeasure->id}}">@lang($interestedToExecuteMeasure->name)</option>
                                @endforeach
                            </select>

                            <div id="{{$key}}-current-glass-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                            @if ($errors->has($key))
                                <span class="help-block">
                                    <strong>{{ $errors->first('example_building_type') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class=" col-sm-3 ">
                        <div class="form-group add-space {{ $errors->has($key) ? ' has-error' : '' }}">
                            <label class=" control-label">
                                <i data-toggle="collapse" data-target="#{{$key}}-heated-rooms-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('woningdossier.cooperation.tool.insulated-glazing.heated-rooms')
                            </label>

                            <select class="form-control" name="{{$key}}[heated-rooms]" >
                                @foreach($interestedToExecuteMeasures as $interestedToExecuteMeasure)
                                    <option @if($interestedToExecuteMeasure->id == old($key)) selected @endif value="{{$interestedToExecuteMeasure->id}}">@lang($interestedToExecuteMeasure->name)</option>
                                @endforeach
                            </select>

                            <div id="{{$key}}-heated-rooms-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                            @if ($errors->has($key))
                                <span class="help-block">
                                    <strong>{{ $errors->first('example_building_type') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class=" col-sm-3 ">
                        <div class="form-group add-space {{ $errors->has($key) ? ' has-error' : '' }}">
                            <label class=" control-label">
                                <i data-toggle="collapse" data-target="#{{$key}}-m2-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('woningdossier.cooperation.tool.insulated-glazing.m2')
                            </label>

                            <select class="form-control" name="{{$key}}[m2]" >
                                @foreach($interestedToExecuteMeasures as $interestedToExecuteMeasure)
                                    <option @if($interestedToExecuteMeasure->id == old($key)) selected @endif value="{{$interestedToExecuteMeasure->id}}">@lang($interestedToExecuteMeasure->name)</option>
                                @endforeach
                            </select>

                            <div id="{{$key}}-m2-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                            @if ($errors->has($key))
                                <span class="help-block">
                                    <strong>{{ $errors->first('example_building_type') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class=" col-sm-3 ">
                        <div class="form-group add-space {{ $errors->has($key) ? ' has-error' : '' }}">
                            <label class=" control-label">
                                <i data-toggle="collapse" data-target="#{{$key}}-total-windows-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('woningdossier.cooperation.tool.insulated-glazing.total-windows')
                            </label>

                            <select class="form-control" name="{{$key}}[total-windows]" >
                                @foreach($interestedToExecuteMeasures as $interestedToExecuteMeasure)
                                    <option @if($interestedToExecuteMeasure->id == old($key)) selected @endif value="{{$interestedToExecuteMeasure->id}}">@lang($interestedToExecuteMeasure->name)</option>
                                @endforeach
                            </select>

                            <div id="{{$key}}-total-windows-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                            @if ($errors->has($key))
                                <span class="help-block">
                                    <strong>{{ $errors->first('example_building_type') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <hr>
            @endforeach

        </div>
    </form>

@endsection