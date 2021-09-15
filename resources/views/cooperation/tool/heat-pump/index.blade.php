@extends('cooperation.frontend.layouts.tool')


@section('step_title')
    {!!  __('heat-pump.title.title') !!}
@endsection

@section('content')
    <form  method="POST" action="{{ route('cooperation.tool.heat-pump.store', compact('cooperation')) }}">
        @csrf
        <div id="start-information" class="mb-4">
            <div class="flex flex-wrap flex-row w-full">
                <div class="w-full">
                    <div class="flex flex-wrap flex-row w-full">
                        <div class="w-full">
                            {!! __('heat-pump.description.title') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @component('cooperation.tool.components.panel', [
            'label' => __('default.buttons.download'),
        ])
            <ol>
                <li>
                    <a download=""
                       href="{{asset('storage/hoomdossier-assets/Maatregelblad_Warmtepomp.pdf')}}">
                        {{ucfirst(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/maatregelblad_warmtepomp.pdf'))))}}
                    </a>
                </li>
            </ol>
        @endcomponent
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