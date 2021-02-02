@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/super-admin/clients/personal-access-tokens.index.header', ['client_name' => $client->name])
            <a href="{{ route('cooperation.admin.super-admin.clients.personal-access-tokens.create', compact('client')) }}"
               class="btn btn-success">
                @lang('cooperation/admin/super-admin/clients/personal-access-tokens.index.header-button')
            </a>
        </div>


        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                    @if(session()->has('token'))
                        <p>@lang('cooperation/admin/super-admin/clients/personal-access-tokens.index.token-created')</p>
                        @component('cooperation.tool.components.alert')
                            {{session('token')->plainTextToken}}
                        @endcomponent
                    @endif

                    <table id="table" class="table table-striped table-responsive table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('cooperation/admin/super-admin/clients/personal-access-tokens.index.table.name')</th>
                            <th>@lang('cooperation/admin/super-admin/clients/personal-access-tokens.index.table.last-used')</th>
                            <th>@lang('cooperation/admin/super-admin/clients/personal-access-tokens.index.table.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($client->tokens as $personalAccessToken)
                            <tr>
                                <td>{{$personalAccessToken->name}}</td>
                                <td>{{$personalAccessToken->last_used_at}}</td>
                                <td>
                                    <form method="post" action="{{route('cooperation.admin.super-admin.clients.personal-access-tokens.destroy', compact('client', 'personalAccessToken'))}}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            @lang('cooperation/admin/super-admin/clients/personal-access-tokens.index.table.delete')
                                        </button>
                                    </form>
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
