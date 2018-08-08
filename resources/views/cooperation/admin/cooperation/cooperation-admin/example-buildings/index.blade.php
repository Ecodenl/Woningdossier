@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <h3>Example buildings</h3>
            </div>
            <div class="col-md-8">
                <a href="{{ route('cooperation.admin.example-buildings.create') }}" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i> Add new</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">

                <table class="table table-responsive table-condensed">
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
                                <a href="{{ route('cooperation.admin.example-buildings.copy', ['id' => $exampleBuilding->id]) }}" class="btn btn-info"><i class="glyphicon glyphicon-copy"></i></a>
                                <a href="{{ route('cooperation.admin.example-buildings.edit', ['id' => $exampleBuilding->id]) }}" class="btn btn-warning"><i class="glyphicon glyphicon-edit"></i></a>
                                <form style="display:inline;" action="{{ route('cooperation.admin.example-buildings.destroy', ['id' => $exampleBuilding->id]) }}" method="post">
                                    {{ method_field("DELETE") }}
                                    {{ csrf_field() }}
                                    <button type="submit" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
@endsection
