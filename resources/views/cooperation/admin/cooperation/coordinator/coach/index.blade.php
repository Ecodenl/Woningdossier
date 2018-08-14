@extends('cooperation.admin.cooperation.coordinator.layouts.app')

@section('coordinator_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.index.header')
            <a href="{{route('cooperation.admin.cooperation.coordinator.coach.create')}}" class="btn btn-md btn-primary pull-right"><span class="glyphicon glyphicon-plus"></span></a>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-bordered table-responsive ">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.index.table.columns.first-name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.index.table.columns.last-name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.index.table.columns.email')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.index.table.columns.role')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.coach.index.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{$user->first_name}}</td>
                                <td>{{$user->last_name}}</td>
                                <td>{{$user->email}}</td>
                                <td>
                                    <?php
                                    $user->roles->map(function ($role) {
                                        echo $role->name .', ';
                                    })
                                    ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-md">
                                        <button type="button" data-toggle="tooltip" title="Edit" class="btn btn-primary"><span class="glyphicon glyphicon-edit"></span></button>
                                        <button type="button" data-toggle="tooltip" title="Remove" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></button>
                                        <button type="button" data-toggle="tooltip" title="Rollen" class="btn btn-primary"><span class="glyphicon glyphicon-user"></span></button>
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


@push('css')
    <link rel="stylesheet" rel="stylesheet" type="text/css" href="{{asset('css/datatables/datatables.min.css')}}">
@push('js')
    <script>
        $(document).ready(function () {
            $('table').DataTable(
                {
                    language: {
                        "sProcessing": "Bezig...",
                        "sLengthMenu": "_MENU_ resultaten weergeven",
                        "sZeroRecords": "Geen resultaten gevonden",
                        "sInfo": "_START_ tot _END_ van _TOTAL_ resultaten",
                        "sInfoEmpty": "Geen resultaten om weer te geven",
                        "sInfoFiltered": " (gefilterd uit _MAX_ resultaten)",
                        "sInfoPostFix": "",
                        "sSearch": "Zoeken:",
                        "sEmptyTable": "Geen resultaten aanwezig in de tabel",
                        "sInfoThousands": ".",
                        "sLoadingRecords": "Een moment geduld aub - bezig met laden...",
                        "oPaginate": {
                            "sFirst": "Eerste",
                            "sLast": "Laatste",
                            "sNext": "Volgende",
                            "sPrevious": "Vorige"
                        },
                        "oAria": {
                            "sSortAscending":  ": activeer om kolom oplopend te sorteren",
                            "sSortDescending": ": activeer om kolom aflopend te sorteren"
                        }
                    },
                    dom: 'Bfrtip',

                }
            );

        })
    </script>
@endpush

