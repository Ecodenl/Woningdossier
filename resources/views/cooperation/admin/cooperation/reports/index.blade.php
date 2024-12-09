@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('woningdossier.cooperation.admin.cooperation.reports.index.title')
])

@section('content')
    @if($filesBeingProcessed > 0)
        <livewire:cooperation.admin.cooperation.reports.file-poller :filesBeingProcessed="$filesBeingProcessed"/>
    @endif

    <div class="w-full data-table">
        <h2>
            @lang('woningdossier.cooperation.admin.cooperation.reports.index.description')
        </h2>
    </div>
    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
            <thead>
                <tr>
                    <th>@lang('woningdossier.cooperation.admin.cooperation.reports.index.table.columns.name')</th>
                    <th>@lang('woningdossier.cooperation.admin.cooperation.reports.index.table.columns.download')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportFileTypeCategory->fileTypes as $fileType)
                    @if(in_array($fileType->short, ['custom-questionnaire-report-anonymized', 'custom-questionnaire-report']))
                        @include('cooperation.admin.cooperation.reports.parts.file-type-questionnaire-table-row')
                    @else
                        <tr>
                            <td>
                                {{$fileType->name}}
                                @php $fileStorage = $fileType->files()->mostRecent()->first(); @endphp
                                @if($fileStorage instanceof \App\Models\FileStorage && ! $fileType->isBeingProcessed())
                                    <a class="in-text block" href="{{route('cooperation.file-storage.download', compact('fileStorage'))}}">
                                        {{$fileType->name}}
                                        ({{$fileStorage->created_at->format('Y-m-d H:i')}})
                                    </a>
                                @endif
                            </td>

                            <td>
                                @if($fileType->isBeingProcessed())
                                    <div title="@lang('woningdossier.cooperation.admin.cooperation.reports.index.table.report-in-queue')">
                                        <button class="btn btn-green flex items-center" type="button" disabled>
                                            @lang('cooperation/frontend/tool.my-plan.downloads.create-report')
                                            <i class="icon-sm icon-ventilation-fan animate-spin-slow ml-1"></i>
                                        </button>
                                    </div>
                                @else
                                    <form action="{{route('cooperation.file-storage.store', ['fileType' => $fileType->short])}}"
                                          method="POST">
                                        @csrf

                                        <button class="btn btn-green" type="submit">
                                            @lang('cooperation/frontend/tool.my-plan.downloads.create-report')
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('js')
    <script type="module">
        document.addEventListener('DOMContentLoaded', () => {
            new DataTable('#table', {
                scrollX: true,
                language: {
                    url: '{{ asset('js/datatables-dutch.json') }}'
                },
                layout: {
                    bottomEnd: {
                        paging: {
                            firstLast: false
                        }
                    }
                },
            });
        });
    </script>
@endpush