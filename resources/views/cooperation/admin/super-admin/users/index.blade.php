@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('admin/super-admin.users.index.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    @include('cooperation.admin.super-admin.users.search')
                </div>
            </div>
        </div>
    </div>
@endsection