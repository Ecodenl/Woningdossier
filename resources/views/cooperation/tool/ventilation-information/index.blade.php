@extends('cooperation.tool.layout')

@section('step_title', \App\Helpers\Translation::translate('ventilation-information.title.title'))

@section('step_content')
    <form class="form-horizontal" method="POST" action="{{ route('cooperation.tool.ventilation-information.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-sm-12">
                <p style="margin-left: -5px">{!! \App\Helpers\Translation::translate('ventilation-information.description.title') !!}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">@lang('woningdossier.cooperation.tool.ventilation-information.downloads.title')</div>
                    <div class="panel-body">
                        <ol>
                            <li><a download="" href="{{asset('storage/hoomdossier-assets/Maatregelblad_Ventilatiebox.pdf')}}">{{ucfirst(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Ventilatiebox.pdf'))))}}</a></li>
                            <li><a download="" href="{{asset('storage/hoomdossier-assets/Maatregelblad_Decentrale_WTW.pdf')}}">{{ucfirst(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Decentrale_WTW.pdf'))))}}</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @if(!\App\helpers\HoomdossierSession::isUserObserving())
        <div class="row">
            <div class="col-md-12">
                <hr>
                <div class="form-group add-space">
                    <div class="">
                        <a class="btn btn-success pull-left" href="{{route('cooperation.tool.wall-insulation.index', ['cooperation' => $cooperation])}}">@lang('default.buttons.prev')</a>
                        <button type="submit" class="btn btn-primary pull-right">
                            @lang('default.buttons.next-page')
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </form>
@endsection

@push('js')
    <script>
        $(document).ready(function() {




        });
    </script>
@endpush