@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/cooperation/questionnaires.index.header')
            <a href="{{route('cooperation.admin.cooperation.questionnaires.create')}}"
               class="btn btn-md btn-primary" style="position: absolute; right: 3rem; top: 0.4rem;">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-responsive ">
                        <thead>
                        <tr>
                            <th>@lang('cooperation/admin/cooperation/questionnaires.index.table.columns.questionnaire-name')</th>
                            <th>@lang('cooperation/admin/cooperation/questionnaires.index.table.columns.step')</th>
                            <th>@lang('cooperation/admin/cooperation/questionnaires.index.table.columns.active')</th>
                            <th>@lang('cooperation/admin/cooperation/questionnaires.index.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($questionnaires as $questionnaire)
                            <tr>
                                <td>{{$questionnaire->name}}</td>
                                <td>{{optional($questionnaire->step)->name}}</td> {{-- Step could be hidden, so we optional it --}}
                                <td>
                                    <input data-active="{{$questionnaire->isActive() ? 'on' : 'off'}}" class="toggle-active"
                                           data-questionnaire-id="{{$questionnaire->id}}" type="checkbox" data-toggle="toggle"
                                           data-on="@lang('cooperation/admin/cooperation/questionnaires.index.table.columns.active-on')"
                                           data-off="@lang('cooperation/admin/cooperation/questionnaires.index.table.columns.active-off')">
                                </td>
                                <td>
                                    <a class="btn btn-success" href="{{route('cooperation.admin.cooperation.questionnaires.edit', compact('questionnaire'))}}">
                                        @lang('cooperation/admin/cooperation/questionnaires.index.table.columns.edit')
                                    </a>

                                    <button data-questionnaire-id="{{$questionnaire->id}}" type="button" class="destroy btn btn-danger">
                                        @lang('cooperation/admin/cooperation/questionnaires.index.table.columns.destroy')
                                    </button>
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
@endpush
@push('js')
    <script src="{{asset('js/bootstrap-toggle.min.js')}}"></script>

    <script>
        $(document).ready(function () {
            var destroyQuestionnaireRoute = '{{route('cooperation.admin.cooperation.questionnaires.destroy', ['questionnaire' => ':questionnaire-id'])}}';

            $(document).on('click', '.destroy', function (event) {
                if (confirm('@lang('cooperation/admin/cooperation/questionnaires.destroy.are-you-sure')')) {
                    $.ajax({
                        url: destroyQuestionnaireRoute.replace(':questionnaire-id', $(this).data('questionnaire-id')),
                        method: 'delete',
                        success: function () {
                            window.location.reload();
                        }
                    });
                } else {
                    event.preventDefault();
                    return false;
                }
            });
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
                    url: '{{route('cooperation.admin.cooperation.questionnaires.set-active')}}',
                    data: {
                        questionnaire_active: $(this).prop('checked'),
                        questionnaire_id: $(this).data('questionnaire-id')
                    }
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
                            "sSortAscending": ": activeer om kolom oplopend te sorteren",
                            "sSortDescending": ": activeer om kolom aflopend te sorteren"
                        }
                    },

                }
            );
        })
    </script>
@endpush

