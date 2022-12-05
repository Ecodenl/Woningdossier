@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3>@lang('cooperation/admin/example-buildings.edit.title', ['name' => $exampleBuilding->name])</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <livewire:cooperation.admin.example-buildings.form :exampleBuilding="$exampleBuilding"/>
                </div>
            </div>
        </div>
    </div>
@endsection
