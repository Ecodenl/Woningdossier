@extends('cooperation.admin.cooperation.coordinator.layouts.app')

@section('coordinator_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.coordinator.questionnaires.index.header')
            <a href="{{route('cooperation.admin.cooperation.coordinator.questionnaires.create')}}" class="btn btn-md btn-primary pull-right"><span class="glyphicon glyphicon-plus"></span></a>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-responsive ">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.questionnaires.index.table.columns.questionnaire-name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.questionnaires.index.table.columns.step')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.questionnaires.index.table.columns.active')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coordinator.questionnaires.index.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($questionnaires as $questionnaire)
                            <tr>
                                <td>{{$questionnaire->name}}</td>
                                <td>{{$questionnaire->step->name}}</td>
                                <td>
                                    <input data-active="{{$questionnaire->isActive() ? 'on' : 'off'}}" class="toggle-active" data-questionnaire-id="{{$questionnaire->id}}"  type="checkbox"  data-toggle="toggle"  data-on="Actief" data-off="Niet actief">
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-success">@lang('woningdossier.cooperation.admin.cooperation.coordinator.questionnaires.index.table.columns.see-results')</button>
                                        <a class="btn btn-success" href="{{route('cooperation.admin.cooperation.coordinator.questionnaires.edit', ['id' => $questionnaire->id])}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.questionnaires.index.table.columns.edit')</a>
                                    </div>
                                </td>
                            </tr>
                        @empty

                        @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endsection


@push('css')
    <link href="{{asset('css/bootstrap-toggle.min.css')}}" rel="stylesheet">


    <link rel="stylesheet" type="text/css" href="{{asset('css/datatables/datatables.min.css')}}">
    @push('js')
        <script src="{{asset('js/bootstrap-toggle.min.js')}}"></script>

        <script>

            $(document).ready(function () {
                var toggleActive = $('.toggle-active');

                $(toggleActive).each(function (index, value) {
                    $(this).bootstrapToggle($(this).data('active'));
                });

                toggleActive.change(function () {
                    console.log($(this));
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "post",
                        url: '{{route('cooperation.admin.cooperation.coordinator.questionnaires.set-active')}}',
                        data: {
                            questionnaire_active: $(this).prop('checked'),
                            questionnaire_id: $(this).data('questionnaire-id')
                        }
                    }).done(function () {
                        console.log('bier!');
                    })
                });
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

                    }
                );
            })
        </script>
    @endpush

