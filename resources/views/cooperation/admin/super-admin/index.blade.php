@extends('cooperation.admin.layouts.app')

@section('content')
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
                                        <i class="glyphicon glyphicon-globe" style="font-size: 6em;"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge" style="font-size: 3em;font-weight: 600;">{{$cooperationCount}}</div>
                                        <div>@lang('woningdossier.cooperation.admin.super-admin.index.cooperations')</div>
                                    </div>
                                </div>
                            </div>
                            {{--<a href="#">--}}
                                {{--<div class="panel-footer">--}}
                                    {{--<span class="pull-left">View Details</span>--}}
                                    {{--<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>--}}
                                    {{--<div class="clearfix"></div>--}}
                                {{--</div>--}}
                            {{--</a>--}}
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
                                        <div>@lang('woningdossier.cooperation.admin.super-admin.index.users')</div>
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
                                        <div class="huge" style="font-size: 3em;font-weight: 600;">{{$buildingCount}}</div>
                                        <div>@lang('woningdossier.cooperation.admin.super-admin.index.buildings')</div>
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