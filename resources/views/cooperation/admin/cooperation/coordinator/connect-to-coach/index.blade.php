@extends('cooperation.admin.cooperation.coordinator.layouts.app')

@section('coordinator_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.index.header')

        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-repsonsive table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.index.table.columns.type-request')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.index.table.columns.first-name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.index.table.columns.last-name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.index.table.columns.email')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.index.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($openConversations as $openConversation)
                            <tr>
                                <td>@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.index.'.$openConversation->request_type)</td>
                                <td>{{$openConversation->getSender($openConversation->id)->first_name}}</td>
                                <td>{{$openConversation->getSender($openConversation->id)->last_name}}</td>
                                <td>{{$openConversation->getSender($openConversation->id)->email}}</td>
                                <td>
                                    <div class="btn-group" role="group" style="min-width: 145px;">
                                        <a href="{{route('cooperation.admin.cooperation.coordinator.conversation-requests.show', ['messageId' => $openConversation->id])}}" class="btn btn-default">
                                            @lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.index.table.columns.see-message')
                                        </a>
                                        <a href="{{route('cooperation.admin.cooperation.coordinator.connect-to-coach.create', ['senderId' => $openConversation->from_user_id])}}" class="btn btn-default">
                                            @lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.index.table.columns.connect-to-coach')
                                        </a>
                                    </div>
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

            $('#table').DataTable({
                responsive: true,
                columnDefs: [
                    {responsivePriority: 4, targets: 4},
                    {responsivePriority: 5, targets: 3},
                    {responsivePriority: 3, targets: 2},
                    {responsivePriority: 2, targets: 1},
                    {responsivePriority: 1, targets: 0}
                ],
            });

            $('.remove').click(function () {
                if (confirm("Weet u zeker dat u de gebruiker wilt verwijderen")) {

                } else {
                    event.preventDefault();
                }
            })
        })


    </script>
@endpush
