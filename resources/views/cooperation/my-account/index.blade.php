@extends('cooperation.my-account.layouts.app')

@section('my_account_content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('my-account.index.header')</div>
                </div>

                <form class="has-address-data" method="POST"
                      action="{{ route('cooperation.my-account.settings.update') }}"
                      autocomplete="off">
                    {{ method_field('PUT')  }}
                    {{ csrf_field() }}

                    <input type="hidden" id="addressid" name="building[addressid]" value="{{$building->bag_addressid}}">

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            @lang('my-account.settings.index.header')
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    {{\App\Helpers\Translation::translate('my-account.settings.index.text')}}
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-6">


                                    <div class="form-group{{ $errors->has('user.first_name') ? ' has-error' : '' }}">
                                        <label for="first_name"
                                               class="control-label">@lang('my-account.settings.index.form.user.first-name')</label>


                                        <input id="first_name" type="text" class="form-control" name="user[first_name]"
                                               value="{{ old('user.first_name', $user->first_name) }}" required
                                               autofocus>

                                        @if ($errors->has('user.first_name'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('user.first_name') }}</strong>
                                    </span>
                                        @endif
                                    </div>

                                </div>
                                <div class="col-sm-6">


                                    <div class="form-group{{ $errors->has('user.last_name') ? ' has-error' : '' }}">
                                        <label for="last_name"
                                               class="control-label">@lang('my-account.settings.index.form.user.last-name')</label>

                                        <input id="last_name" type="text" class="form-control" name="user[last_name]"
                                               value="{{ old('last_name', $user->last_name) }}" required autofocus>

                                        @if ($errors->has('user.last_name'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('user.last_name') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-sm-6">


                                    <div class="form-group{{ $errors->has('user.phone_number') ? ' has-error' : '' }}">
                                        <label for="phone_number"
                                               class="control-label">@lang('my-account.settings.index.form.user.phone_number')</label>


                                        <input id="phone_number" type="text" class="form-control"
                                               name="user[phone_number]"
                                               value="{{ old('phone_number', $user->phone_number) }}" autofocus>

                                        @if ($errors->has('user.phone_number'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('user.phone_number') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-sm-12">
                                    <h3>@lang('my-account.settings.index.header-building')</h3>
                                </div>

                                <div class="col-sm-4">

                                    <div class="form-group{{ $errors->has('building.postal_code') ? ' has-error' : '' }}">
                                        <label for="building.postal_code" class="control-label">
                                            @lang('my-account.settings.index.form.building.postal-code')
                                        </label>
                                        <input type="text" class="form-control" name="building[postal_code]"
                                               id="postal_code"
                                               value="{{ old('building.postal_code', $building->postal_code) }}"
                                               required autofocus>

                                        @if ($errors->has('building.postal_code'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('building.postal_code') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>


                                <div class="col-sm-4">
                                    <div class="form-group{{ $errors->has('building.house_number') ? ' has-error' : '' }}">
                                        <label for="building.number"
                                               class="control-label">@lang('my-account.settings.index.form.building.number')</label>

                                        <input type="text" class="form-control" name="building[house_number]"
                                               id="number"
                                               value="{{ old('building.house_number', $building->number) }}" required
                                               autofocus>

                                        @if ($errors->has('building.house_number'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('building.house_number') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group{{ $errors->has('building.extension') ? ' has-error' : '' }}">
                                        <label for="building.extension" class="control-label">
                                            @lang('my-account.settings.index.form.building.extension')
                                        </label>

                                        <input type="text" class="form-control" name="building[extension]"
                                               id="house_number_extension"
                                               value="{{ old('building.extension', $building->extension) }}" autofocus>

                                        @if ($errors->has('building.extension'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('building.extension') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-sm-6">


                                    <div class="form-group{{ $errors->has('building.street') ? ' has-error' : '' }}">
                                        <label for="street"
                                               class="control-label">@lang('my-account.settings.index.form.building.street')</label>

                                        <input type="text" class="form-control" name="building[street]" id="street"
                                               value="{{ old('building.street', $building->street) }}" required
                                               autofocus>

                                        @if ($errors->has('building.street'))
                                            <span class="help-block">
                                    <strong>{{ $errors->first('building.street') }}</strong>
                                </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-6">


                                    <div class="form-group{{ $errors->has('building.city') ? ' has-error' : '' }}">
                                        <label for="building.city"
                                               class="control-label">@lang('my-account.settings.index.form.building.city')</label>

                                        <input type="text" class="form-control" name="building[city]" id="city"
                                               value="{{ old('building.city', $building->city) }}" required autofocus>

                                        @if ($errors->has('building.city'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('building.city') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-primary">
                                            @lang('my-account.settings.index.form.submit')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <form method="POST" action="{{ route('cooperation.my-account.hoom-settings.update', $account->id) }}"
                      autocomplete="off">
                    {{ method_field('PUT')  }}
                    {{ csrf_field() }}


                    <div class="panel panel-default">
                        <div class="panel-heading">
                            {{\App\Helpers\Translation::translate('my-account.hoom-settings.index.header')}}
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <p>{{\App\Helpers\Translation::translate('my-account.hoom-settings.index.text')}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">


                                    <div class="form-group{{ $errors->has('account.email') ? ' has-error' : '' }}">
                                        <label for="email"
                                               class="control-label">@lang('my-account.hoom-settings.index.form.account.e-mail')</label>


                                        <input id="email" type="email" class="form-control" name="account[email]"
                                               value="{{ old('account.email', $account->email) }}" required autofocus>

                                        @if ($errors->has('account.email'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('account.email') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <h3>@lang('my-account.hoom-settings.index.header-password')</h3>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group{{ $errors->has('account.current_password') ? ' has-error' : '' }}">
                                        <label for="current_password"
                                               class="control-label">@lang('my-account.hoom-settings.index.form.account.current-password')</label>


                                        <input id="current_password" type="password" class="form-control"
                                               name="account[current_password]">

                                        @if ($errors->has('account.current_password'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('account.current_password') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group{{ $errors->has('account.password') ? ' has-error' : '' }}">
                                        <label for="password"
                                               class="control-label">@lang('my-account.hoom-settings.index.form.account.new-password')</label>


                                        <input id="password" type="password" class="form-control"
                                               name="account[password]">

                                        @if ($errors->has('account.password'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('account.password') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group{{ $errors->has('account.password') || $errors->has('account.password_confirmation') ? ' has-error' : '' }}">
                                        <label for="password-confirm"
                                               class="control-label">@lang('my-account.hoom-settings.index.form.account.new-password-confirmation')</label>


                                        <input id="password-confirm" type="password" class="form-control"
                                               name="account[password_confirmation]">
                                        @if ($errors->has('account.password') || $errors->has('account.password_confirmation'))
                                            <span class="help-block">
                                    <strong>{{ $errors->first('account.password') }}</strong>
                                    <strong>{{ $errors->first('account.password_confirmation') }}</strong>
                                </span>
                                        @endif
                                    </div>
                                </div>

                            </div>
                            <!-- password change section -->

                            <hr>
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-primary">
                                            @lang('my-account.hoom-settings.index.form.submit')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>


                <div id="reset-modal" class="modal fade" role="dialog">
                    <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">@lang('my-account.settings.reset-file.modal.title')</h4>
                            </div>
                            <div class="modal-body">
                                @lang('my-account.settings.reset-file.modal.text')
                            </div>
                            <div class="modal-footer">
                                <form class="form-horizontal" method="POST"
                                      action="{{ route('cooperation.my-account.settings.reset-file', ['cooperation' => $cooperation]) }}">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="input_sources[id][]"
                                           value="{{\App\Models\InputSource::findByShort('resident')->id}}">
                                    <button type="button"
                                            class="reset-account pull-left btn btn-warning">@lang('my-account.settings.reset-file.modal.reset-resident')</button>
                                </form>
                                <form class="form-horizontal" method="POST"
                                      action="{{ route('cooperation.my-account.settings.reset-file', ['cooperation' => $cooperation]) }}">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="input_sources[id][]"
                                           value="{{\App\Models\InputSource::findByShort('resident')->id}}">
                                    <input type="hidden" name="input_sources[id][]"
                                           value="{{\App\Models\InputSource::findByShort('coach')->id}}">
                                    <button type="button"
                                            class="reset-account btn btn-warning">@lang('my-account.settings.reset-file.modal.reset-both')</button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">@lang('my-account.settings.reset-file.header')</div>

                    <div class="panel-body">
                        @lang('my-account.settings.reset-file.description')
                        <div class="form-group">
                            <div class="col-sm-12">
                                <label for="reset-file"
                                       class="control-label">@lang('my-account.settings.reset-file.label')</label>

                                <a data-toggle="modal" data-target="#reset-modal" class="btn btn-danger">
                                    @lang('my-account.settings.reset-file.submit')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                @can('delete-own-account')
                    <div class="panel panel-default">
                        <div class="panel-heading">@lang('my-account.settings.destroy.header')</div>

                        <div class="panel-body">
                            <form method="POST"
                                  action="{{ route('cooperation.my-account.settings.destroy', ['cooperation' => $cooperation]) }}">
                                {{ method_field('DELETE') }}
                                {{ csrf_field() }}

                                <div class="row">
                                    <div class="col-sm-12">


                                        <div class="form-group">
                                            <label for="delete-account"
                                                   class="control-label">@lang('my-account.settings.destroy.label')</label>

                                            <button type="submit" id="delete-account" class="btn btn-danger">
                                                @lang('my-account.settings.destroy.submit')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endcan

                {{-- Notification settings --}}
                <div class="panel panel-default">
                    <div class="panel-heading">
                        @lang('my-account.notification-settings.index.header')
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="table"
                                       class="table table-striped table-responsive table-bordered compact nowrap">
                                    <thead>
                                    <tr>
                                        <th>@lang('my-account.notification-settings.index.table.columns.name')</th>
                                        <th>@lang('my-account.notification-settings.index.table.columns.interval')</th>
                                        <th>@lang('my-account.notification-settings.index.table.columns.last-notified-at')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($notificationSettings as $i => $notificationSetting)
                                        <tr>
                                            <td>{{ $notificationSetting->type->name }}</td>
                                            <td>

                                                <form action="{{route('cooperation.my-account.notification-settings.update', $notificationSetting->id)}}"
                                                      method="post">
                                                    {{csrf_field()}}
                                                    {{method_field('put')}}
                                                    <select name="notification_setting[{{$notificationSetting->id}}][interval_id]"
                                                            class="form-control change-interval">
                                                        @foreach($notificationIntervals as $notificationInterval)
                                                            <option @if(old('notification_setting.interval_id', $notificationSetting->interval_id) == $notificationInterval->id) selected="selected"
                                                                    @endif value="{{$notificationInterval->id}}">{{$notificationInterval->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            </td>
                                            <td>{{ is_null($notificationSetting->last_notified_at) ? __('my-account.notification-settings.index.table.never-sent') : $notificationSetting->last_notified_at->format('Y-m-d') }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{--Access--}}
                <div class="panel panel-default">
                    <div class="panel-heading">
                        @lang('my-account.access.index.header')
                    </div>

                    <div class="panel-body">
                        @if($conversationRequests->isNotEmpty())
                            <div class="row">
                                <div class="col-sm-12">
                                    <form id="allow-access-form"
                                          action="{{route('cooperation.my-account.access.allow-access')}}"
                                          method="post">
                                        {{csrf_field()}}
                                        <div class="form-group {{ $errors->has('allow_access') ? ' has-error' : '' }}">
                                            <label for="allow_access">
                                                <input id="allow_access" name="allow_access" type="checkbox"
                                                       @if(old('allow_access') && old('allow_access') == 'on' || $conversationRequests->contains('allow_access', true))
                                                       checked="checked"
                                                        @endif>
                                                @lang('my-account.access.index.form.allow_access', ['cooperation' => \App\Models\Cooperation::find(\App\Helpers\HoomdossierSession::getCooperation())->name])
                                            </label>
                                            @if ($errors->has('allow_access'))
                                                <span class="help-block">
                                    <strong>{{ $errors->first('allow_access') }}</strong>
                                </span>
                                            @endif
                                            <p>@lang('my-account.access.index.text-allow-access')</p>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="table"
                                       class="table table-striped table-responsive table-bordered compact nowrap">
                                    <thead>
                                    <tr>
                                        <th>@lang('my-account.access.index.table.columns.coach')</th>
                                        <th>@lang('my-account.access.index.table.columns.actions')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($buildingPermissions as $i => $buildingPermission)
                                        <form id="revoke-access-{{$buildingPermission->id}}"
                                              action="{{route('cooperation.messages.participants.revoke-access')}}"
                                              method="post">
                                            {{csrf_field()}}
                                            <input type="hidden" name="user_id"
                                                   value="{{$buildingPermission->user_id}}">
                                            <input type="hidden" name="building_owner_id"
                                                   value="{{$buildingPermission->building_id}}">
                                        </form>
                                        <tr>
                                            <td>{{ $buildingPermission->user->getFullName() }}</td>
                                            <td>
                                                <a data-form-id="revoke-access-{{$buildingPermission->id}}"
                                                   class="revoke-access btn btn-danger"><i
                                                            class="glyphicon glyphicon-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        $(document).ready(function () {
            $('.change-interval').change(function () {
                $(this).parent().submit();
            });

            $('.revoke-access').click(function () {
                if (confirm('Weet u zeker dat u deze gebruiker de toegang wilt ontzetten ?')) {
                    var formId = $(this).data('form-id');
                    $('form#' + formId).submit();
                }
            });

            $('#allow_access').change(function () {
                // if access gets turned of, we want to show them a alert
                // else we dont!
                if ($(this).prop('checked') === false) {
                    if (confirm('Weet u zeker dat u de toegang wilt ontzeggen voor elk gekoppelde coach ?')) {
                        $('#allow-access-form').submit();
                    } else {
                        // otherwise this may seems weird, so on cancel. we check the box again.
                        $(this).prop('checked', true);
                    }
                } else {
                    $('#allow-access-form').submit();
                }
            });


            var areYouSure = '@lang('my-account.settings.reset-file.are-you-sure')';
            $('.reset-account').click(function (event) {
                if (confirm(areYouSure)) {
                    $(this).closest('form').submit();
                } else {
                    // so the opened modal will close.
                    $('.modal').modal('hide');
                    event.preventDefault();
                    return false;
                }
            });

            var userCooperationCount = '{{$account->users()->count()}}';

            var areYouSureToDestroy = '@lang('my-account.settings.destroy.are-you-sure.delete-from-cooperation')';

            if (userCooperationCount === 1) {
                areYouSureToDestroy = '@lang('my-account.settings.destroy.are-you-sure.complete-delete')';
            }

            $('#delete-account').click(function (event) {
                if (confirm(areYouSureToDestroy)) {
                    $(this).closest('form').submit();
                } else {
                    event.preventDefault();
                    return false;
                }
            })
        })
    </script>
@endpush
