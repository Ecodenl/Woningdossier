@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.super-admin.translations.index.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-bordered compact nowrap table-responsive">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.translations.index.table.columns.name')</th>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.translations.index.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($steps as $step)
                            <tr>
                                <td>{{$step->name}}</td>
                                <td>
                                    <a class="btn btn-default" href="{{route('cooperation.admin.super-admin.translations.edit', ['step-slug' => $step->slug])}}">
                                        @lang('woningdossier.cooperation.admin.super-admin.translations.index.table.see')
                                    </a>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                            <tr>
                                <td>@lang('woningdossier.cooperation.admin.super-admin.translations.index.table.main-translations')</td>
                                <td>
                                    <a class="btn btn-default" href="{{route('cooperation.admin.super-admin.translations.edit', ['step-slug' => 'general'])}}">
                                        @lang('woningdossier.cooperation.admin.super-admin.translations.index.table.see')
                                    </a>
                                </td>
                            </tr>
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