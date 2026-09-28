@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/smart-twin.index.title')
])

@section('content')
    <div class="w-full data-table">
        <h2>
            @lang('cooperation/admin/super-admin/smart-twin.index.description')
        </h2>
    </div>
    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
            <thead>
                <tr>
                    <th>@lang('cooperation/admin/super-admin/smart-twin.index.table.columns.cooperation')</th>
                    <th>@lang('cooperation/admin/super-admin/smart-twin.index.table.columns.building')</th>
                    <th>@lang('cooperation/admin/super-admin/smart-twin.index.table.columns.type')</th>
                    <th>@lang('cooperation/admin/super-admin/smart-twin.index.table.columns.flow')</th>
                    <th>@lang('cooperation/admin/super-admin/smart-twin.index.table.columns.updated-at')</th>
                    <th>@lang('cooperation/admin/super-admin/smart-twin.index.table.columns.available-until')</th>
                    <th>@lang('cooperation/admin/super-admin/smart-twin.index.table.columns.actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($fileStorages as $fileStorage)
                    <tr>
                        <td>{{ $fileStorage->cooperation?->name }}</td>
                        <td>
                            {{ $fileStorage->building?->getAddress() }}
                            <span class="in-text block">#{{ $fileStorage->building_id }}</span>
                        </td>
                        <td>{{ $fileStorage->fileType->name }}</td>
                        <td>{{ $fileStorage->inputSource?->name }}</td>
                        <td>{{ $fileStorage->updated_at?->format('Y-m-d H:i') }}</td>
                        <td>
                            {{ $fileStorage->available_until?->format('Y-m-d H:i') }}
                            @if($fileStorage->available_until?->isPast())
                                <span class="in-text block">
                                    @lang('cooperation/admin/super-admin/smart-twin.index.table.expired')
                                </span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('cooperation.admin.super-admin.smart-twin.download', ['fileStorageId' => $fileStorage->id]) }}"
                               class="btn btn-blue mb-2 mr-2">
                                @lang('cooperation/admin/super-admin/smart-twin.index.table.download')
                            </a>

                            @if($fileStorage->fileType->short === \App\Services\SmartTwin\SmartTwinFileTypes::ADVICE_RAW)
                                <form action="{{ route('cooperation.admin.super-admin.smart-twin.reprocess', ['fileStorageId' => $fileStorage->id]) }}"
                                      method="POST" class="inline">
                                    @csrf

                                    <button class="reprocess btn btn-green" type="submit">
                                        @lang('cooperation/admin/super-admin/smart-twin.index.table.reprocess')
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('js')
    <script type="module" nonce="{{ $cspNonce }}">
        document.addEventListener('DOMContentLoaded', function () {
            new DataTable('#table', {
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

            document.querySelectorAll('.reprocess').forEach(function (button) {
                button.addEventListener('click', function (event) {
                    if (! confirm('@lang('cooperation/admin/super-admin/smart-twin.reprocess.confirm')')) {
                        event.preventDefault();
                    }
                });
            });
        });
    </script>
@endpush
