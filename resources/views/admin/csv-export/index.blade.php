@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Admin</div>

                    <div class="panel-body">

                        <div class="row">
                            <div class="col-sm-12">
                                @lang('woningdossier.cooperation.admin.csv-export.description')
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <a href="{{route('admin.csv-export.download.by-year')}}" class="btn btn-primary">@lang('woningdossier.cooperation.admin.csv-export.download.by-year')</a>
                                <a href="{{route('admin.csv-export.download.by-measure')}}" class="btn btn-primary">@lang('woningdossier.cooperation.admin.csv-export.download.by-measure') </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
