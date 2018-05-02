@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.my-plan.title'))

@section('step_content')

    <div class="row">
        <div class="col-md-12">
            <h1>@lang('woningdossier.cooperation.tool.my-plan.title')</h1>
            <p>@lang('woningdossier.cooperation.tool.my-plan.description')</p>
        </div>
    </div>

    <form class="form-horizontal" action="{{ route('cooperation.tool.my-plan.store', ['cooperation' => $cooperation]) }}" method="post">
        {{ csrf_field() }}
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
                                <strong>Besparing m<sup>3</sup> gas</strong>
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
                                <input type="checkbox" @if($advice->planned)checked="checked"@endif name="advice[{{ $advice->id }}][planned]" />
                            </div>
                            <div class="col-md-2">
                                {{ $advice->measureApplication->measure_name }}
                            </div>
                            <div class="col-md-1">
                                {{ \App\Helpers\NumberFormatter::format($advice->costs) }}
                            </div>
                            <div class="col-md-2">
                                {{ \App\Helpers\NumberFormatter::format($advice->savings_gas) }}
                            </div>
                            <div class="col-md-2">
                                {{ \App\Helpers\NumberFormatter::format($advice->savings_electricity) }}
                            </div>
                            <div class="col-md-2">
                                {{ \App\Helpers\NumberFormatter::format($advice->savings_money) }}
                            </div>
                            <div class="col-md-1">
                                {{ $advice->year }}
                            </div>
                            <div class="col-md-1">
                                <input type="text" maxlength="4" size="4" class="form-control" name="advice[{{ $advice->id }}][planned_year]" value="{{ $advice->planned_year }}" />
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endforeach
    </form>


    <div class="row">
        <div class="plan-preview col-md-12">
            <h2>Uw persoonlijke meerjarenonderhoudsplan</h2>
            <ul id="years">

            </ul>
        </div>
    </div>

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

            $("select, input[type=radio], input[type=text], input[type=checkbox]").change(function(){


                var form = $(this).closest("form").serialize();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.my-plan.store', [ 'cooperation' => $cooperation ]) }}',
                    data: form,
                    success: function(data){
                        $("ul#years").html("");
                        $.each(data, function(year, steps){
                            var header = "<h1>" + year + "</h1>";
                            var table = "<table class=\"table table-condensed table-responsive table-striped\"><thead><tr><th>Maatregel</th><th>Kosten</th><th>Besparing gas</th><th>Besparing elektra</th><th>Besparing euro</th></tr></thead><tbody>";

                            var totalCosts = 0;
                            var totalSavingsGas = 0;
                            var totalSavingsElectricity = 0;
                            var totalSavingsMoney = 0;

                            $.each(steps, function(stepName, stepMeasures){

                                $.each(stepMeasures, function(i, stepData){

                                    totalCosts += parseFloat(stepData.costs);
                                    totalSavingsGas += parseFloat(stepData.savings_gas);
                                    totalSavingsElectricity += parseFloat(stepData.savings_electricity);
                                    totalSavingsMoney += parseFloat(stepData.savings_money);

                                    table += "<tr><td>" + stepData.measure + "</td><td>&euro; " + Math.round(stepData.costs) + "</td><td>" + Math.round(stepData.savings_gas) + " m<sup>3</sup></td><td>" + Math.round(stepData.savings_electricity) + " kWh</td><td>&euro; " + Math.round(stepData.savings_money) + "</td></tr>";
                                });

                            });

                            table += "<tr><td><strong>Totaal</strong></td><td>&euro; " + Math.round(totalCosts) + "</td><td>" + Math.round(totalSavingsGas) + " m<sup>3</sup></td><td>" + Math.round(totalSavingsElectricity) + " kWh</td><td>&euro; " + Math.round(totalSavingsMoney) + "</td></tr>";
                            table += "<tr><td colspan=\"5\"></td></tr>";

                            table += "</tbody></table>";

                            $("ul#years").append("<li>" + header + table + "</li>");
                        });



                        @if(App::environment('local'))
                        console.log(data);
                        @endif
                    }
                })
            });
            // Trigger the change event so it will load the data
            $('form').find('*').filter(':input:visible:first').trigger('change');
        });


    </script>
@endpush

