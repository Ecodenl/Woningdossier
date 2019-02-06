@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.index.header')

        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-responsive table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.index.table.columns.type-request')</th>
                            <th>Naam</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.index.table.columns.email')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.index.table.columns.requested-on')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.index.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($buildings as $building)
                            <?php $conversationRequest = \App\Models\PrivateMessage::forMyCooperation()->conversationRequest($building->id)->first(); ?>
                            @if($conversationRequest instanceof \App\Models\PrivateMessage)
                                <tr>
                                    <td>@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.index.'.$conversationRequest->request_type)</td>
                                    <td>{{$building->user->getFullName()}}</td>
                                    <td>{{$building->user->email}}</td>
                                    <td>{{$conversationRequest->created_at}}</td>
                                    <td>
                                        <div class="btn-group" role="group" style="min-width: 145px;">
                                            <a href="{{route('cooperation.admin.cooperation.coordinator.messages.public.edit', ['buildingId' => $building->id])}}" class="btn btn-default">
                                                @lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.index.table.columns.see-message')
                                            </a>
                                            <a href="{{route('cooperation.admin.cooperation.coordinator.messages.private.edit', ['buildingId' => $building->id])}}" class="btn btn-default">
                                                @lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.index.table.columns.talk-to-coach')
                                            </a>
                                            <a href="{{route('cooperation.admin.cooperation.coordinator.connect-to-coach.create', ['buildingId' => $building->id])}}" class="btn btn-default">
                                                @lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.index.table.columns.connect-to-coach')
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
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
