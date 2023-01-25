@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.example-buildings.create.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <livewire:cooperation.admin.example-buildings.form :exampleBuilding="null"/>
                </div>
            </div>
        </div>
    </div>
@endsection