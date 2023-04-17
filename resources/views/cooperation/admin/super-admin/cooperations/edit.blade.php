@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.super-admin.cooperations.edit.header', ['name' => $cooperationToEdit->name])
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <livewire:cooperation.admin.super-admin.cooperations.form :cooperationToEdit="$cooperationToEdit"/>
                </div>
            </div>
        </div>
    </div>
@endsection