@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <th>@lang('woningdossier.cooperation.admin.super-admin.key-figures.index.header')</th>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <h2>@lang('woningdossier.cooperation.admin.super-admin.key-figures.index.sections.general')</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-responsive table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.key-figures.index.table.title')</th>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.key-figures.index.table.key-figure')</th>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.key-figures.index.table.key-figure-unit')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($keyfigures as $step => $keyfigureData)
                            @foreach($keyfigureData as $kengetalConstant => $kengetal)
                            <?php
                                $translationKey = 'key-figures.' . $step . '.' .$kengetalConstant.'.title';
                                $kengetalTitle = __($translationKey);
                            ?>
                            <tr @if($translationKey == $kengetalTitle)class="bg-danger"@endif>
                                <td>{!! $kengetalTitle !!}</td>
                                <td>{{$kengetal}}</td>
                                <td>{!! __('key-figures.' . $step . '.' . $kengetalConstant .'.unit') !!}</td>
                            </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <h2>@lang('woningdossier.cooperation.admin.super-admin.key-figures.index.sections.measure_applications')</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-striped table-responsive table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.key-figures.index.table.measure_applications.measure-name')</th>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.key-figures.index.table.measure_applications.costs')</th>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.key-figures.index.table.measure_applications.cost-unit')</th>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.key-figures.index.table.measure_applications.minimal-costs')</th>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.key-figures.index.table.measure_applications.maintenance-interval')</th>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.key-figures.index.table.measure_applications.maintenance-unit')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($measureApplications as $measureApplication)
                            <tr>
                                <td>{{$measureApplication->measure_name}}</td>
                                <td>{{$measureApplication->costs}}</td>
                                <td>{{$measureApplication->cost_unit}}</td>
                                <td>{{$measureApplication->minimal_costs}}</td>
                                <td>{{$measureApplication->maintenance_interval}}</td>
                                <td>{{$measureApplication->maintenance_unit}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

{{--
@push('js')
    <script>
        $(document).ready(function () {
            $('#table').dataTable();
        });
    </script>
@endpush
--}}