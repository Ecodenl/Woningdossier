@extends('cooperation.admin.coach.layouts.app')

@section('coach_content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.coach.buildings.header')</div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-responsive table-condensed">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.city')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.street')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.owner')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.actions')</th>
                            <th>@lang('woningdossier.cooperation.admin.coach.buildings.index.table.columns.status')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($buildingPermissions as $buildingPermission)
                            <tr>
                                <td>{{ $buildingPermission->building->city }}</td>
                                <td>{{ $buildingPermission->building->city }}</td>
                                <td>{{ $buildingPermission->building->user->first_name .' '. $buildingPermission->building->user->last_name}}</td>
                                <td>
                                    <a href="{{ route('cooperation.admin.coach.buildings.fill-for-user', ['id' => $buildingPermission->building->id]) }}" class="btn btn-warning"><i class="glyphicon glyphicon-edit"></i></a>
                                    <form style="display:inline;" action="{{ route('cooperation.admin.cooperation.cooperation-admin.example-buildings.destroy', ['id' => $buildingPermission->building->id]) }}" method="post">
                                        {{ method_field("DELETE") }}
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i></button>
                                    </form>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                            @lang('woningdossier.cooperation.admin.coach.buildings.index.table.status')
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @foreach($buildingCoachStatuses as $buildingStatus)
                                                <form action="{{route('cooperation.admin.coach.buildings.set-building-status')}}" method="post">
                                                    <input type="hidden" name="building_coach_status_id" value="{{$buildingStatus->id}}">
                                                    <input type="hidden" name="building_id" value="{{$buildingPermission->building->id}}">
                                                    <li><a href="javascript:;" onclick="parentNode().submit()" href="">{{$buildingStatus->name}}</a></li>
                                                </form>
                                            @endforeach
                                        </ul>
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
            // put the label text from the selected option inside the input for ux
            var takeAction = $('#take-action');
            var input = $(takeAction).find('input.form-control');
            var dropdown = $(takeAction).find('input[type=radio]');
            var inputPrefix = '@lang('woningdossier.cooperation.conversation-requests.edit.form.selected-option')';
            $(dropdown).change(function () {
                var radioLabel = $('input[type=radio]:checked').parent().text().trim();
                if (coachConversationTranslation != radioLabel) {
                    window.location = '{{url('/aanvragen')}}' + '/' + $('input[type=radio]:checked').val()
                }
                // we lower the case after the check is done, otherwise it would fail in any case
                radioLabel.toLowerCase();
                $(input).val();
                $(input).val(inputPrefix +' '+ radioLabel);
            });
            $(dropdown).trigger('change');
            // when the form gets submited check if the user agreed with the agreement
            // if so submit, else do nuthing
            $('form').on('submit', function () {
                if ($('input[name=agreement]').is(':checked')  == false) {
                    if (confirm('Weet u zeker dat u geen toesteming wilt geven ?')) {
                    } else {
                        event.preventDefault();
                    }
                }
            })
    </script>
@endpush