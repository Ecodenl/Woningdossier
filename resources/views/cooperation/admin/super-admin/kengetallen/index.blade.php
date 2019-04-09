@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <th>@lang('woningdossier.cooperation.admin.super-admin.kengetallen.index.header')</th>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">


                    <table id="table" class="table table-striped table-responsive table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.kengetallen.index.table.title')</th>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.kengetallen.index.table.kengetal')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($kengetallen as $kengetalConstant => $kengetal)
                            <?php $kengetalTitle = __('woningdossier.cooperation.admin.super-admin.kengetallen.index.'.$kengetalConstant.'.title') ?>
                            <tr>
                                <td>{{$kengetalTitle}}</td>
                                <td>{{$kengetal}}</td>
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
            $('table').dataTable();
        });
    </script>
@endpush