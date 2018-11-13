@extends('cooperation.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                @if (Auth::user()->buildings->first()->id != \App\Helpers\HoomdossierSession::getBuilding())
                    <div class="col-sm-6">
                        @component('cooperation.tool.components.alert')
                            @lang('woningdossier.cooperation.tool.filling-for', [
                                'first_name' => \App\Models\User::find(\App\Models\Building::find(\App\Helpers\HoomdossierSession::getBuilding())->user_id)->first_name,
                                'input_source_name' => \App\Models\InputSource::find(\App\Helpers\HoomdossierSession::getInputSourceValue())->name
                            ])
                        @endcomponent
                    </div>
                    <div class="col-sm-6">
                        @component('cooperation.tool.components.alert')
                            @lang('woningdossier.cooperation.tool.current-building-address', [
                                'street' => $building->street,
                                'number' => $building->number,
                                'extension' => $building->extension,
                                'zip_code' => $building->postal_code,
                                'city' => $building->city
                            ])
                        @endcomponent
                    </div>
                @else
                    <?php
                        if(!isset($building)) {
                            $building = \App\Models\Building::find(\App\Helpers\HoomdossierSession::getBuilding());
                        }
                    ?>
                    @component('cooperation.tool.components.alert')
                        @lang('woningdossier.cooperation.tool.current-building-address', [
                            'street' => $building->street,
                            'number' => $building->number,
                            'extension' => $building->extension,
                            'zip_code' => $building->postal_code,
                            'city' => $building->city
                        ])
                    @endcomponent
                @endif

                @include('cooperation.tool.progress')
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3>
                            @yield('step_title', '')
                        </h3>

                        @if(!in_array(Route::currentRouteName(), ['cooperation.tool.index', 'cooperation.tool.my-plan.index']))
                            <button id="submit-form-top-right" class="pull-right btn btn-primary">
                                @if(in_array(Route::currentRouteName(), ['cooperation.tool.ventilation-information.index', 'cooperation.tool.heat-pump.index']))
                                    @lang('default.buttons.next-page')
                                @else
                                    @lang('default.buttons.next')
                                @endif
                            </button>
                        @else
                            @if(in_array(Route::currentRouteName(), ['cooperation.tool.my-plan.index']))
                                <a href="{{ route('cooperation.tool.my-plan.export', ['cooperation' => $cooperation]) }}" class="pull-right btn btn-primary">
                                    @lang('woningdossier.cooperation.tool.my-plan.download')
                                </a>
                            @endif
                        @endif
                        <div class="clearfix"></div>
                    </div>

                    <div class="panel-body">
                        @yield('step_content', '')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $('#submit-form-top-right').click(function () {
            // There will only be 1 form inside the panel body, submit it
            $('.panel-body form button[type=submit]').click();
        })
    </script>
    <script src="{{ asset('js/are-you-sure.js') }}"></script>

    @if(!in_array(Route::currentRouteName(), ['cooperation.tool.my-plan.index']))
    <script>
        $("form.form-horizontal").areYouSure();
    </script>
    @endif

@endpush