@extends('cooperation.admin.layouts.app')

@push('meta')
    @if($anyFilesBeingProcessed)
        <meta http-equiv="refresh" content="5">
    @endif
@endpush

@if($anyFilesBeingProcessed)
    @section('top-container')
        @component('cooperation.tool.components.alert')
            @lang('woningdossier.cooperation.admin.cooperation.reports.generate.success')
        @endcomponent
    @endsection
@endif

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
                                        <form action="{{route('cooperation.file-storage.store', ['fileType' => $fileType->short])}}" method="post">
                                            {{csrf_field()}}
                                            <button
                                                    @if($fileType->isBeingProcessed()) disabled="disabled" type="button" data-toggle="tooltip"
                                                    title="{{\App\Helpers\Translation::translate('woningdossier.cooperation.admin.cooperation.reports.index.table.report-in-queue')}}"
                                                    @else
                                                    type="submit"
                                                    @endif
                                                    class="btn btn-{{$fileType->isBeingProcessed()  ? 'warning' : 'primary'}}"
                                            >
                                                {{ \App\Helpers\Translation::translate('my-plan.download.title') }}
                                                @if($fileType->isBeingProcessed() )
                                                    <span class="glyphicon glyphicon-repeat fast-right-spinner"></span>
                                                @endif
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <ul>
                                            <?php $file = $fileType->files()->mostRecent()->first();?>
                                            @if($file instanceof \App\Models\FileStorage)
                                                <li>
                                                    <a @if(!$fileType->isBeingProcessed() )
                                                       href="{{route('cooperation.file-storage.download', [
                                                        'fileType' => $fileType->short,
                                                        'fileStorageFilename' => $file->filename
                                                    ])}}" @endif>{{$fileType->name}} ({{$file->created_at->format('Y-m-d H:i')}})</a>
                                                </li>
                                            @endif
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