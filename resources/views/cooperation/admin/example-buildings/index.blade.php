@extends('cooperation.admin.layouts.app')

@section('content')
    @if(Hoomdossier::user()->hasRoleAndIsCurrentRole('super-admin'))
        <livewire:cooperation.admin.example-buildings.csv-export :cooperation="$cooperation"/>
    @endif

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.example-buildings.index.header')
            <a href="{{ route('cooperation.admin.example-buildings.create') }}" class="btn btn-success">
                <i class="glyphicon glyphicon-plus"></i>
                @lang('cooperation/admin/example-buildings.index.create-button')
            </a>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-responsive table-bordered compact nowrap">
                        <thead>
                            <tr>
                                <td>@lang('cooperation/admin/example-buildings.index.table.name')</td>
                                <td>@lang('cooperation/admin/example-buildings.index.table.order')</td>
                                <td>@lang('cooperation/admin/example-buildings.index.table.cooperation')</td>
                                <td>@lang('cooperation/admin/example-buildings.index.table.default')</td>
                                <td>@lang('cooperation/admin/example-buildings.index.table.actions')</td>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($exampleBuildings as $exampleBuilding)
                            <tr>
                                <td>{{ $exampleBuilding->name }}</td>
                                <td>{{ $exampleBuilding->order }}</td>
                                <td>@if($exampleBuilding->cooperation instanceof \App\Models\Cooperation){{ $exampleBuilding->cooperation->name }}@else - @endif</td>
                                <td>@if($exampleBuilding->is_default)<i class="glyphicon glyphicon-check"></i>@endif</td>
                                <td>
                                    <a data-toggle="tooltip" title="Kopiëren" href="{{ route('cooperation.admin.example-buildings.copy', compact('exampleBuilding')) }}" class="btn btn-info">
                                        <i class="glyphicon glyphicon-copy"></i>
                                    </a>
                                    <a data-toggle="tooltip" title="Bewerken" href="{{ route('cooperation.admin.example-buildings.edit', compact('exampleBuilding')) }}" class="btn btn-warning"><i class="glyphicon glyphicon-edit"></i></a>
                                    <form style="display:inline;" method="POST"
                                          action="{{ route('cooperation.admin.example-buildings.destroy', compact('exampleBuilding')) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button data-toggle="tooltip" title="@lang('default.buttons.destroy')"
                                                type="submit" class="btn btn-danger destroy-example-building">
                                            <i class="glyphicon glyphicon-remove"></i>
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
            $('[data-toggle="tooltip"]').tooltip();
            $('#table').DataTable({
                responsive: true,
                pageLength: 50,
                order: [],
                columnDefs: [
                    {responsivePriority: 2, targets: 1},
                    {responsivePriority: 1, targets: 0}
                ]
            });

            $(document).on('click', '.destroy-example-building', function (event) {
                if (! confirm('@lang('cooperation/admin/example-buildings.destroy.confirm')')) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    return false;
                }
            });
        });
    </script>
@endpush