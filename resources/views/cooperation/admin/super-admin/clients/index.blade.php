@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/clients.index.header'),
    'panelLink' => route('cooperation.admin.super-admin.clients.create')
])

@section('content')
    <div class="panel panel-default">

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-responsive table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('cooperation/admin/super-admin/clients.column-translations.name')</th>
                            <th>@lang('cooperation/admin/super-admin/clients.index.table.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($clients as $client)
                            <tr>
                                <td>{{$client->name}}</td>
                                <td>
                                    <a href="{{route('cooperation.admin.super-admin.clients.edit', compact('cooperation', 'client'))}}" class="btn btn-default">@lang('cooperation/admin/super-admin/clients.index.table.edit')</a>
                                    <a href="{{route('cooperation.admin.super-admin.clients.personal-access-tokens.index', compact('cooperation', 'client'))}}" class="btn btn-default">@lang('cooperation/admin/super-admin/clients.index.table.api-tokens')</a>
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
    <script type="module">
        document.addEventListener('DOMContentLoaded', function () {
            $('#table').DataTable({
                responsive: true,
                columnDefs: [
                    {responsivePriority: 2, targets: 1},
                    {responsivePriority: 1, targets: 0}
                ],
            });
        });

    </script>
@endpush
