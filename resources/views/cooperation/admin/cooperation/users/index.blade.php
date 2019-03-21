@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.users.index.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-bordered compact nowrap table-responsive" style="width: 100%">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.users.index.table.columns.date')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.users.index.table.columns.name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.users.index.table.columns.street-house-number')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.users.index.table.columns.zip-code')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.users.index.table.columns.city')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.users.index.table.columns.status')</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php /** @var \App\Models\User $user */ ?>
                        @foreach($users as $user)
                            <?php $building = $user->buildings()->withTrashed()->first(); ?>

                            @if($building instanceof \App\Models\Building)
                            <tr>
                                <td data-sort="{{$user->created_at instanceof \Carbon\Carbon ? strtotime($user->created_at->format('d-m-Y')) : '-'}}">
                                    {{$user->created_at instanceof \Carbon\Carbon ? $user->created_at->format('d-m-Y') : '-'}}
                                </td>
                                <td>{{$user->getFullName()}}</td>
                                <td>
                                    <a href="{{route('cooperation.admin.buildings.show', ['buildingId' => $building->id])}}">
                                        {{$building->street}} {{$building->house_number}} {{$building->house_number_ext}}
                                    </a>
                                </td>
                                <td>{{$building->postal_code}}</td>
                                <td>
                                    {{$building->city}}
                                </td>
                                <td>
                                    {{\App\Models\BuildingCoachStatus::getCurrentStatusForBuildingId($building->id)}}
                                </td>
                            </tr>
                            @else
                                {{Log::debug('View: cooperation.admin.cooperation.users.index: There is a user id '.$user->id.' without a building')}}
                            @endif
                        @endforeach
                        </tbody>
                    </table>
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
                    { responsivePriority: 1 },
                    { responsivePriority: 2 },
                    { responsivePriority: 3 },
                    { responsivePriority: 4 },
                    { responsivePriority: 6 },
                    { responsivePriority: 5 }
                ]
            });
        })
    </script>
@endpush