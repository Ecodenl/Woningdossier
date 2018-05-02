@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.heat-pump-information.title'))


@section('step_content')
    <form class="form-horizontal" method="POST" action="{{ route('cooperation.tool.heat-pump-information.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        <div id="solar-panels">
            <div class="row">
                <div class="col-sm-12">
                    <h4 style="margin-left: -5px">@lang('woningdossier.cooperation.tool.heat-pump-information.description')</h4>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">@lang('woningdossier.cooperation.tool.heat-pump-information.downloads.title')</div>
                        <div class="panel-body">
                            <ol>
                                <li><a download="" href="{{asset('storage/hoomdossier-assets/info-warmtepomp.docx')}}">{{ucfirst(str_replace('-', ' ', basename(asset('storage/hoomdossier-assets/info-warmtepomp.docx'))))}}</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

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

        </div>
    </form>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });
    </script>
@endpush