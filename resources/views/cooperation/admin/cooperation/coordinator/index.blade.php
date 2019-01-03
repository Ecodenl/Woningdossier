@extends('cooperation.admin.cooperation.coordinator.layouts.app')

@section('coordinator_content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.cooperation.coordinator.index.header')</div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-responsive table-striped table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.index.table.columns.first-name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.index.table.columns.last-name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.index.table.columns.email')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.index.table.columns.role')</th>
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
                                            echo ucfirst($role->human_readable_name).', ';
                                        })
                                    ?>
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
@endpush

@push('js')
    <script>
        $(document).ready(function () {
            $('#table').DataTable(
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

                }
            );
        })
    </script>
@endpush
