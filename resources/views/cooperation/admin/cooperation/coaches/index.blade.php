@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.coaches.index.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-bordered compact nowrap table-responsive">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.index.table.columns.name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.index.table.columns.street-house-number')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.index.table.columns.zip-code')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.index.table.columns.city')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.index.table.columns.email')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.index.table.columns.roles')</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php /** @var \App\Models\User $user */ ?>
                        @foreach($users as $user)
                            <?php
                                $building = $user->building;
                                $userIsAuthUser = $user->id == \App\Helpers\Hoomdossier::user()->id;
                            ?>
                            @if($building instanceof \App\Models\Building)
                            <tr>
                                <td>{{$user->getFullName()}}</td>
                                <td>
                                    @if($userIsAuthUser)
                                        <p>{{$building->street}} {{$building->number}} {{$building->extension}}</p>
                                    @else
                                    <a href="{{route('cooperation.admin.cooperation.coaches.show', ['id' => $user->id])}}">
                                        {{$building->street}} {{$building->number}} {{$building->extension}}
                                    </a>
                                    @endif
                                </td>
                                <td>{{$building->postal_code}}</td>
                                <td>
                                    {{$building->city}}
                                </td>
                                <td>
                                    {{$user->account->email}}
                                </td>
                                <td>
                                    {{implode(',', $user->roles->pluck('human_readable_name')->toArray())}}
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
            var table = $('table');
            table.DataTable();
        })
    </script>
@endpush