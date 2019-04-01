@extends('cooperation.admin.layouts.app')

@section('content')
    @if(isset($breadcrumbs))
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb">
                    @foreach($breadcrumbs as $breadcrumb)
                        <li {{Route::currentRouteName() == $breadcrumb['route'] ? 'class="active"' : ''}}>
                            @if(Route::currentRouteName() == $breadcrumb['route'])
                                <a href="{{$breadcrumb['url']}}">{{$breadcrumb['name']}}</a>
                            @else
                                {{$breadcrumb['name']}}
                            @endif

                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    @endif
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.super-admin.index.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-lg-4 col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="glyphicon glyphicon-eye-open" style="font-size: 6em;"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge"
                                             style="font-size: 3em;font-weight: 600;">{{$cooperationAdminCount}}</div>
                                        <div>
                                            @lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index.cooperation-admin-count')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="glyphicon glyphicon-eye-close" style="font-size: 6em;"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge" style="font-size: 3em;font-weight: 600;">{{$userCount}}</div>
                                        <div>
                                            @lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index.coordinator-count')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="glyphicon glyphicon-user" style="font-size: 6em;"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge" style="font-size: 3em;font-weight: 600;">{{$userCount}}</div>
                                        <div>
                                            @lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index.user-count')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="glyphicon glyphicon-home" style="font-size: 6em;"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge"
                                             style="font-size: 3em;font-weight: 600;">{{$buildingCount}}</div>
                                        <div>Buildings</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection