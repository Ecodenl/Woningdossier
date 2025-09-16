@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('woningdossier.cooperation.admin.cooperation.messages.index.header')
])

@section('content')
    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
            <thead>
                <tr>
                    <th>@lang('woningdossier.cooperation.admin.cooperation.messages.index.table.columns.most-recent-message-date')</th>
                    <th>@lang('woningdossier.cooperation.admin.cooperation.messages.index.table.columns.sender-name')</th>
                    <th>@lang('woningdossier.cooperation.admin.cooperation.messages.index.table.columns.street-house-number')</th>
                    <th>@lang('woningdossier.cooperation.admin.cooperation.messages.index.table.columns.zip-code')</th>
                    <th>@lang('woningdossier.cooperation.admin.cooperation.messages.index.table.columns.city')</th>
                    <th>@lang('woningdossier.cooperation.admin.cooperation.messages.index.table.columns.unread-messages')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($buildings as $building)
                        <?php
                        $mostRecentMessage = $building->privateMessages->last();
                        ?>
                    <tr>
                        <td data-sort="{{strtotime($mostRecentMessage->created_at->format('d-m-Y H:i'))}}">
                            {{$mostRecentMessage->created_at->format('d-m-Y H:i')}}
                        </td>
                        <td>{{$mostRecentMessage->getSender()}}</td>
                        <td>
                            <a class="in-text" href="{{route('cooperation.admin.buildings.show', compact('building'))}}">
                                {{$building->street}} {{$building->number}} {{$building->extension}}
                            </a>
                        </td>
                        <td>{{$building->postal_code}}</td>
                        <td>
                            {{$building->city}}
                        </td>
                        <td>
                            {{\App\Models\PrivateMessageView::getTotalUnreadMessagesCountByBuildingForAuthUser($building)}}
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
                scrollX: true,
                order: [[0, "desc"]],
                // responsive: true,
                // columnDefs: [
                //     {responsivePriority: 1, targets: 1},
                //     {responsivePriority: 2, targets: 5}
                // ],
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