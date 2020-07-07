@extends('cooperation.tool.layout')

@section('step_title')
    {!!  \App\Helpers\Translation::translate('heat-pump.title.title') !!}
@endsection


@section('step_content')
    <form  method="POST" action="{{ route('cooperation.tool.heat-pump.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        <div id="start-information">
            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-12">
                            {!! \App\Helpers\Translation::translate('heat-pump.description.title') !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="panel panel-primary">
                            <div class="panel-heading">@lang('woningdossier.cooperation.tool.heat-pump-information.downloads.title')</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        {!! \App\Helpers\Translation::translate('woningdossier.cooperation.tool.heat-pump-information.downloads.title') !!}
                                    </div>
                                </div>
                                <ol>
                                    <li><a download=""
                                           href="{{asset('storage/hoomdossier-assets/Maatregelblad_Warmtepomp.pdf')}}">{{ucfirst(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/maatregelblad_warmtepomp.pdf'))))}}</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('js')
    <script>
        // var gasUsageTapWater = $('#gas-usage-for-warm-tapwater');
        // var currentGasUsage = $('#current-gas-usage');
        //
        // $(document).ready(function () {
        //     $('#wanted-heat-source').change( function () {
        //         // Get the selected option
        //         var wantedHeatSource = $('#wanted-heat-source option:selected').text();
        //         // Get the current text and change it
        //         $('#wanted-heat-source-text').text('Warmtepomp met '+ wantedHeatSource.toLowerCase()+' al warmtebron');
        //
        //     });
        //
        //     $('#wanted-heat-source').trigger('change');
        // });
    </script>
@endpush