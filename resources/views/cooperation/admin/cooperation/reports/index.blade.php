@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.cooperation.reports.index.title')</div>

        <div class="panel-body">

            <div class="row">
                <div class="col-sm-12">
                    <h2>@lang('woningdossier.cooperation.admin.cooperation.reports.index.description')</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">

                    <table id="table" class="table table-striped table-bordered compact nowrap table-responsive" style="width: 100%">
                        <thead>
                        <tr>
                            <th>{{\App\Helpers\Translation::translate('woningdossier.cooperation.admin.cooperation.reports.index.table.columns.name')}}</th>
                            <th>{{\App\Helpers\Translation::translate('woningdossier.cooperation.admin.cooperation.reports.index.table.columns.download')}}</th>
                            <th>{{\App\Helpers\Translation::translate('woningdossier.cooperation.admin.cooperation.reports.index.table.columns.available-report')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($reportFileTypeCategory->fileTypes as $fileType)
                                <tr>
                                    <td>{{$fileType->name}}</td>

                                    <td>
                                        <a
                                            @if($fileType->isBeingProcessed() || session()->has('file_type_'.$fileType->id))
                                                disabled="disabled"
                                                href="#" data-toggle="tooltip" title="{{\App\Helpers\Translation::translate('woningdossier.cooperation.admin.cooperation.reports.index.table.report-in-queue')}}"
                                            @else
                                                href="{{route('cooperation.admin.cooperation.reports.generate', ['fileTypeId' => $fileType->id])}}"
                                            @endif
                                            class="btn btn-{{$fileType->isBeingProcessed() || session()->has('file_type_'.$fileType->id) ? 'warning' : 'primary'}}">

                                            {{\App\Helpers\Translation::translate('woningdossier.cooperation.admin.cooperation.reports.index.table.generate-btn')}}
                                            @if($fileType->isBeingProcessed() || session()->has('file_type_'.$fileType->id))
                                                <span class="glyphicon glyphicon-repeat fast-right-spinner"></span>
                                            @endif
                                        </a>
                                    </td>
                                    <td>
                                        <ul>
                                            @foreach($fileType->files as $file)
                                                <li>
                                                    <a @if(!$fileType->isBeingProcessed() || session()->has('file_type_'.$fileType->id)) href="" @endif>{{$fileType->name}} ({{$file->created_at->format('Y-m-d H:i')}})</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
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
        $('[data-toggle="tooltip"]').tooltip();
    </script>
@endpush