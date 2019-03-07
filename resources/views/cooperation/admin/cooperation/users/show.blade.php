@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.users.show.header', [
                'name' => $user->getFullName(),
                'street-and-number' => $building->street.' '.$building->house_number.$building->house_number_extension,
                'zipcode-and-city' => $building->postal_code.' '.$building->city
            ])
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>@lang('woningdossier.cooperation.admin.coach.buildings.edit.form.status')</label>
                        <div class="input-group" id="current-building-status">
                            <input disabled
                                   placeholder="@lang('woningdossier.cooperation.admin.cooperation.users.show.status.label')"
                                   type="text" class="form-control disabled" aria-label="...">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                    @lang('woningdossier.cooperation.admin.cooperation.users.show.status.button')
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <?php $buildingCoachStatuses = $building->buildingCoachStatuses ?>
                                    @foreach(__('woningdossier.building-coach-statuses') as $buildingCoachStatusKey => $buildingCoachStatusName)
                                        @if(!empty(\App\Models\BuildingCoachStatus::getCurrentStatusName($building->id)))
                                            <input type="hidden" value="{{$buildingCoachStatusKey}}" data-coach-status="{{$buildingCoachStatusName}}">
                                            <li>
                                                <a href="javascript:;" @if(\App\Models\BuildingCoachStatus::getCurrentStatusName($building->id) == $buildingCoachStatusName) id="current" @endif >
                                                    {{$buildingCoachStatusName}}
                                                </a>
                                            </li>
                                        @else
                                            <li>
                                                <a href="javascript:;" id="current">@lang('woningdossier.building-coach-statuses.awaiting-status')</a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div><!-- /btn-group -->
                        </div><!-- /input-group -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            var table = $('table');
            table.DataTable({
                responsive: true,
                columns: [
                    {responsivePriority: 1},
                    {responsivePriority: 2},
                    {responsivePriority: 3},
                    {responsivePriority: 4},
                    {responsivePriority: 6},
                    {responsivePriority: 5}
                ]
            });

            // put the label text from the selected option inside the input for ux
            var buildingCoachStatus = $('#current-building-status');
            var input = $(buildingCoachStatus).find('input.form-control');
            var currentStatus = $(buildingCoachStatus).find('li a[id=current]');
            var status = $(buildingCoachStatus).find('li a');
            var dropdown = $(buildingCoachStatus).find('ul');

            var inputValPrefix = '{{__('woningdossier.cooperation.admin.cooperation.users.show.status.label')}} ';
            $(input).val(inputValPrefix + $(currentStatus).text().trim());

            $(status).on('click', function () {
                var buildingCoachStatus = $(dropdown).find('[data-coach-status="' + $(this).text().trim() + '"]').val();

                $('input[name=building_coach_status]').val(buildingCoachStatus);
                $(input).val(inputValPrefix + $(this).text().trim());
            });
        })
    </script>
@endpush