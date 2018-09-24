@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.reports.title')</div>

                    <div class="panel-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <h2>@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.reports.description')</h2>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <ul>
                                    <li>
                                        <a href="{{ route('cooperation.admin.cooperation.cooperation-admin.reports.download.by-year') }}">@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.reports.download.by-year') (CSV)</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('cooperation.admin.cooperation.cooperation-admin.reports.download.by-measure') }}">@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.reports.download.by-measure') (CSV)</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
