@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('woningdossier.cooperation.admin.choose-roles.header')</div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <h4>@lang('woningdossier.cooperation.admin.choose-roles.text')</h4>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            @foreach($user->getRoleNames() as $i => $roleName)
                            <div class="col-sm-3">
                                <form action="">
                                    <a href="{{\App\Helpers\RoleHelper::geturlByRoleName($roleName)}}">
                                        <div class="choose-roles-panel panel panel-default">
                                            <div class="panel-heading">{{$user->getHumanReadableRoleName($roleName)}}</div>
                                        </div>
                                    </a>
                                </form>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
