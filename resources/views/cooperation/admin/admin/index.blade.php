@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Admin</div>

                    <div class="panel-body">

                        <div class="row">
                            <div class="col-sm-12">

                                <ul>
                                    <li><a href="{{ route('cooperation.admin.cooperation.cooperation-admin.example-buildings.index') }}">@lang('woningdossier.cooperation.admin.navbar.example-buildings')</a></li>
                                    <li><a href="{{ route('cooperation.admin.cooperation.cooperation-admin.reports.index') }}">@lang('woningdossier.cooperation.admin.navbar.reports')</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
