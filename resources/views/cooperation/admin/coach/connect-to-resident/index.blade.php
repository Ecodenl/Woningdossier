@extends('cooperation.admin.layouts.app')

@section('coach_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.coach.connect-to-resident.index.header')

        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.coach.connect-to-resident.index.table.columns.first-name')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.connect-to-resident.index.table.columns.last-name')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.connect-to-resident.index.table.columns.email')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.connect-to-resident.index.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($buildingsFromBuildingCoachStatuses as $building)
                                <?php $user = \App\Models\User::find($building->user_id); ?>
                                <tr>
                                    <td>{{$user->first_name}}</td>
                                    <td>{{$user->last_name}}</td>
                                    <td>{{$user->email}}</td>
                                    <td>
                                        <a href="{{route('cooperation.admin.coach.connect-to-resident.create', ['userId' => $user->id])}}" class="btn btn-success">@lang('woningdossier.cooperation.admin.coach.connect-to-resident.index.table.columns.start-conversation')<span class="glyphicon glyphicon-envelope"></span></a>
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
                        {responsivePriority: 5, targets: 3},
                        {responsivePriority: 3, targets: 2},
                        {responsivePriority: 2, targets: 1},
                        {responsivePriority: 1, targets: 0}
                    ],
                });

            })
        </script>
@endpush