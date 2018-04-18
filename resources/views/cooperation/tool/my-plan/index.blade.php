@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.my-plan.title'))

@section('step_content')

    <div class="row">
        <div class="col-md-12">
            <h1>@lang('woningdossier.cooperation.tool.my-plan.title')</h1>
            <p>@lang('woningdossier.cooperation.tool.my-plan.description')</p>
        </div>
    </div>

    @foreach($advices as $measureType => $stepAdvices)
        <div class="row">
            <div class="col-md-12">
                <h2>@if($measureType == 'energy_saving') Energiebesparende maatregelen @else Onderhoud @endif</h2>
            </div>
            @foreach($stepAdvices as $step => $advicesForStep)

            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ $step }}</div>
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-md-1">
                                <strong>Interesse</strong>
                            </div>
                            <div class="col-md-2">
                                <strong>Maatregel</strong>
                            </div>
                            <div class="col-md-1">
                                <strong>Kosten</strong>
                            </div>
                            <div class="col-md-2">
                                <strong>Besparing m3 gas</strong>
                            </div>
                            <div class="col-md-2">
                                <strong>Besparing kWh elektra</strong>
                            </div>
                            <div class="col-md-2">
                                <strong>Besparing in euro</strong>
                            </div>
                            <div class="col-md-1">
                                <strong>Geadviseerde uitvoering</strong>
                            </div>
                            <div class="col-md-1">
                                <strong>Planning</strong>
                            </div>

                        </div>

                        @foreach($advicesForStep as $advice)
                        <div class="row">
                            <div class="col-md-1">
                                <input type="checkbox" @if($advice->planned)checked="checked"@endif name="advice[{{ $advice->id }}[planned]" />
                            </div>
                            <div class="col-md-2">
                                {{ $advice->measureApplication->measure_name }}
                            </div>
                            <div class="col-md-1">
                                {{ $advice->costs }}
                            </div>
                            <div class="col-md-2">
                                {{ $advice->savings_gas }}
                            </div>
                            <div class="col-md-2">
                                {{ $advice->savings_electricity }}
                            </div>
                            <div class="col-md-2">
                                {{ $advice->savings_money }}
                            </div>
                            <div class="col-md-1">
                                {{ $advice->year }}
                            </div>
                            <div class="col-md-1">
                                <input type="text" maxlength="4" size="4" class="form-control" name="advice[{{ $advice->id }}[planned_year]" value="{{ $advice->year }}" />
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endforeach


    <div class="row">
        <div class="plan-preview col-md-12">
            <h2>Uw persoonlijke meerjarenonderhoudsplan</h2>
            <ul>
                <li>Bla</li>
                <li>die</li>
                <li>bla</li>
            </ul>
        </div>
    </div>

@endsection