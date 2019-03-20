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
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.index.table.columns.date')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.index.table.columns.name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.index.table.columns.street-house-number')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.index.table.columns.zip-code')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.index.table.columns.city')</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php /** @var \App\Models\User $user */ ?>
                        @foreach($users as $user)
                            <?php $building = $user->buildings()->first(); ?>
                            <tr>
                                <td>{{$user->created_at instanceof \Carbon\Carbon ? $user->created_at->format('d-m-Y') : __('woningdossier.cooperation.admin.cooperation.coaches.index.table.columns.no-known-created-at')}}</td>
                                <td>{{$user->getFullName()}}</td>
                                <td>
                                    <a href="{{route('cooperation.admin.cooperation.coaches.show', ['id' => $user->id])}}">
                                        {{$building->street}} {{$building->house_number}} {{$building->house_number_ext}}
                                    </a>
                                </td>
                                <td>{{$building->postal_code}}</td>
                                <td>
                                    {{$building->city}}
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
            var table = $('table');
            table.DataTable();
        })
    </script>
@endpush