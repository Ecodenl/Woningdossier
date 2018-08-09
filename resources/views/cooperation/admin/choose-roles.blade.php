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
                            <?php $totalUserRoles = $user->getRoleNames()->count() ?>
                            @foreach($user->getRoleNames() as $i => $roleName)
                            <div class="@if($totalUserRoles == 2) col-sm-6 @elseif($totalUserRoles == 3) col-sm-4 @else col-sm-3 @endif">
                                <form action="">
                                    <a href="{{\App\Helpers\RoleHelper::geturlByRoleName($roleName)}}">
                                        <div class="choose-roles-panel panel panel-default">
                                            <div class="panel-body">
                                                <h2 class="text-center">
                                                    <span class="glyphicon glyphicon-user"></span>
                                                </h2>
                                                <h3 class="text-center">
                                                    {{ucfirst($user->getHumanReadableRoleName($roleName))}}
                                                </h3>
                                            </div>
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
