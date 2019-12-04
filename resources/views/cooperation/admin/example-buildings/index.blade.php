@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.example-buildings.index.header')
            <a href="{{ route('cooperation.admin.example-buildings.create') }}" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i> Add new</a>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-responsive table-bordered compact nowrap">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Order</th>
                                <th>Cooperation</th>
                                <th>Default</th>
                                <th>Actions</th>
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
                                    <a data-toggle="tooltip" title="Kopiëren" href="{{ route('cooperation.admin.example-buildings.copy', ['id' => $exampleBuilding->id]) }}" class="btn btn-info">
                                        <i class="glyphicon glyphicon-copy"></i>
                                    </a>
                                    <a data-toggle="tooltip" title="Bewerken" href="{{ route('cooperation.admin.example-buildings.edit', ['id' => $exampleBuilding->id]) }}" class="btn btn-warning"><i class="glyphicon glyphicon-edit"></i></a>
                                    <form style="display:inline;" action="{{ route('cooperation.admin.example-buildings.destroy', ['id' => $exampleBuilding->id]) }}" method="post">
                                        {{ method_field("DELETE") }}
                                        {{ csrf_field() }}
                                        <button data-toggle="tooltip" title="Verwijderen" button type="submit" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i></button>
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
        });
    </script>
@endpush

