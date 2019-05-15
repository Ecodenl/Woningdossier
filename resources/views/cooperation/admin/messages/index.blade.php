@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.cooperation.messages.index.header')</div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-responsive table-bordered compact nowrap" style="width: 100%;">
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
                               $mostRecentMessage = \App\Models\PrivateMessage::where('building_id', $building->id)->get()->last();
                            ?>
                            <tr>
                                <td data-sort="{{strtotime($mostRecentMessage->created_at->format('d-m-Y H:i'))}}">
                                    {{$mostRecentMessage->created_at->format('d-m-Y H:i')}}
                                </td>
                                <td>{{$mostRecentMessage->getSender()}}</td>
                                <td>
                                    <a href="{{route('cooperation.admin.buildings.show', ['buildingId' => $building->id])}}">
                                        {{$building->street}} {{$building->number}} {{$building->extension}}
                                    </a>
                                </td>
                                <td>{{$building->postal_code}}</td>
                                <td>
                                    {{$building->city}}
                                </td>
                                <td>
                                    {{\App\Models\PrivateMessageView::getTotalUnreadMessagesCountByBuildingId($building->id)}}
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
        $(document).ready(function () {
            $('table').DataTable({
                order: [[ 0, "desc" ]],
            });
        })
    </script>
@endpush