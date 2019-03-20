@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.cooperation.reports.title')</div>

        <div class="panel-body">

            <div class="row">
                <div class="col-sm-12">
                    <h2>@lang('woningdossier.cooperation.admin.cooperation.reports.description')</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <ul>
                        <li>
                            <a href="{{ route('cooperation.admin.cooperation.reports.download.by-year') }}">@lang('woningdossier.cooperation.admin.cooperation.reports.download.by-year') (CSV)</a>
                        </li>
                        <li>
                            <a href="{{ route('cooperation.admin.cooperation.reports.download.by-measure') }}">@lang('woningdossier.cooperation.admin.cooperation.reports.download.by-measure') (CSV)</a>
                        </li>
                        @if(Auth::user()->hasRoleAndIsCurrentRole(['cooperation-admin']))
                            <li>
                                <a href="{{ route('cooperation.admin.cooperation.reports.download.questionnaire-results') }}">@lang('woningdossier.cooperation.admin.cooperation.reports.download.download-questionnaire-results') (CSV)</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection