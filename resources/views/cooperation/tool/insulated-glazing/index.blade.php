@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.insulated-glazing.title'))


@section('step_content')

    <form class="form-horizontal" method="POST" action="{{ route('cooperation.tool.insulated-glazing.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        <div id="information">
            <div class="row">
                <div class="col-sm-6">
                    <h4>@lang('woningdossier.cooperation.tool.insulated-glazing.information.interested-to-measure.title')</h4>
                    <div class="form-group add-space {{ $errors->has('glass_in_lead') ? ' has-error' : '' }}">
                        <label class=" control-label">
                            <i data-toggle="collapse" data-target="#glass-in-lead-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.general-data.building-type.example-building-type')
                        </label>

                        <select class="form-control" name="glass_in_lead" >
                            @foreach($interestedToExecuteMeasures as $interestedToExecuteMeasure)
                                <option @if($interestedToExecuteMeasure->id == old('glass_in_lead')) selected @endif value="{{$interestedToExecuteMeasure->id}}">@lang($interestedToExecuteMeasure->name)</option>
                            @endforeach
                        </select>

                        <div id="glass-in-lead-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                            And i would like to have it to...
                        </div>

                        @if ($errors->has('glass_in_lead'))
                            <span class="help-block">
                                <strong>{{ $errors->first('example_building_type') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection