@extends('cooperation.admin.cooperation.coordinator.layouts.app')

@section('coordinator_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.coordinator.building-access.edit.header', ['street' => $building->street, 'postal_code' => $building->postal_code])

        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-responsive table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.building-access.edit.table.columns.name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.building-access.edit.table.columns.email')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.building-access.edit.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($usersThatHaveAccessToBuilding as $i => $user)
                            <tr>
                                <td>{{ $user->first_name }} {{$user->last_name}}</td>
                                <td>{{ $user->email}}</td>
                                <td>
                                    <form action="{{route('cooperation.admin.cooperation.coordinator.building-access.destroy')}}" method="post">
                                        {{csrf_field()}}
                                        <input type="hidden" name="_method" value="delete">
                                        <input type="hidden" name="building_id" value="{{$building->id}}">
                                        <input type="hidden" name="user_id" value="{{$user->id}}">
                                        <button type="submit" class="remove btn btn-danger"><i class="glyphicon glyphicon-ban-circle"></i></button>
                                    </form>
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
                    {responsivePriority: 3, targets: 2},
                    {responsivePriority: 2, targets: 1},
                    {responsivePriority: 1, targets: 0}
                ],
            });

            $('.remove').click(function () {
                if (confirm("Weet u zeker dat u de toegang voor deze gebruiker wilt ontzeggen")) {

                } else {
                    event.preventDefault();
                    return false;
                }
            })
        })


    </script>
@endpush
