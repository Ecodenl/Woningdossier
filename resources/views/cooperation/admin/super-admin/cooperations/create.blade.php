@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('woningdossier.cooperation.admin.super-admin.cooperations.create.header')
])

@section('content')
    <div class="panel panel-default">

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <livewire:cooperation.admin.super-admin.cooperations.form :cooperation="$cooperation"/>
                </div>
            </div>
        </div>
    </div>
@endsection
