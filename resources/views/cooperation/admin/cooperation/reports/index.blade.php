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
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-bordered compact nowrap table-responsive" style="width: 100%">
                        <thead>
                        <tr>
                            <th>{{\App\Helpers\Translation::translate('woningdossier.cooperation.admin.cooperation.reports.table.columns.name')}}</th>
                            <th>{{\App\Helpers\Translation::translate('woningdossier.cooperation.admin.cooperation.reports.table.columns.download')}}</th>
                            <th>{{\App\Helpers\Translation::translate('woningdossier.cooperation.admin.cooperation.reports.table.columns.available-report')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($downloadables as $downloadable)
                                <tr>
                                    <td>{{$downloadable['name']}}</td>
                                    <td><a href="{{$downloadable['url']}}" class="btn btn-primary">Download</a></td>
                                    <td></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $('table').dataTable({responsive: true});
    </script>
@endpush