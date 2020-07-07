@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index.header', [
                'cooperation' => $cooperationToManage->name
            ])
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="glyphicon glyphicon-user" style="font-size: 6em;"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge"
                                                 style="font-size: 3em;font-weight: 600;">{{$residentCount}}</div>
                                            <div>
                                                @lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index.resident-count')
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
                                            <div class="huge"
                                                 style="font-size: 3em;font-weight: 600;">{{$coachCount}}</div>
                                            <div>
                                                @lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index.coach-count')
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
                                            <div class="huge"
                                                 style="font-size: 3em;font-weight: 600;">{{$coordinatorCount}}</div>
                                            <div>
                                                @lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index.coordinator-count')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-offset-8 col-sm-4">
                    <div class="panel panel-default">
                        <div class="panel-heading"><span class="glyphicon glyphicon-cog"></span>Settings</div>
                        <div class="panel-body">
                            @if($cooperationToManage->slug != 'hoom')
                                <a data-toggle="modal" data-target="{{"#destroy-cooperation-{$cooperationToManage->id}" }}" class="btn btn-danger">
                                    @lang('woningdossier.cooperation.admin.super-admin.cooperations.index.destroy')
                                </a>
                            @endif
                            @component('cooperation.tool.components.modal', ['id' => "destroy-cooperation-{$cooperationToManage->id}", 'title' => "Verwijder {$cooperationToManage->name}"])
                                <form action="{{route('cooperation.admin.super-admin.cooperations.destroy', ['cooperationToDestroy' => $cooperationToManage])}}" method="POST">
                                    {{csrf_field()}}
                                    {{method_field('delete')}}
                                    <p class="text-danger">
                                        @lang('woningdossier.cooperation.admin.super-admin.cooperations.index.modal.text', ['cooperation' => $cooperationToManage->name])
                                    </p>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">
                                        @lang('woningdossier.cooperation.admin.super-admin.cooperations.index.modal.cancel')
                                    </button>

                                    <button type="submit" class="btn btn-danger pull-right">
                                        @lang('woningdossier.cooperation.admin.super-admin.cooperations.index.modal.destroy')
                                    </button>
                                </form>
                            @endcomponent
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection