@extends('cooperation.my-account.layouts.app')

@section('my_account_content')
    <form class="has-address-data" method="POST" action="{{ route('cooperation.my-account.settings.update') }}"
          autocomplete="off">
        {{ method_field('PUT')  }}
        {{ csrf_field() }}

        <input type="hidden" id="addressid" name="building[addressid]" value="{{$building->bag_addressid}}">

        <div class="panel panel-default">
            <div class="panel-heading">
                @lang('woningdossier.cooperation.my-account.settings.index.header-user')
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">


                        <div class="form-group{{ $errors->has('user.first_name') ? ' has-error' : '' }}">
                            <label for="first_name"
                                   class="control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.user.first-name')</label>


                            <input id="first_name" type="text" class="form-control" name="user[first_name]"
                                   value="{{ old('first_name', $user->first_name) }}" required autofocus>

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
                                   class="control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.user.last-name')</label>

                            <input id="last_name" type="text" class="form-control" name="user[last_name]"
                                   value="{{ old('last_name', $user->last_name) }}" required autofocus>

                            @if ($errors->has('user.last_name'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('user.last_name') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-6">


                        <div class="form-group{{ $errors->has('user.email') ? ' has-error' : '' }}">
                            <label for="email"
                                   class="control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.user.e-mail')</label>


                            <input id="email" type="email" class="form-control" name="user[email]"
                                   value="{{ old('email', $user->email) }}" required autofocus>

                            @if ($errors->has('user.email'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('user.email') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">


                        <div class="form-group{{ $errors->has('user.phone_number') ? ' has-error' : '' }}">
                            <label for="phone_number"
                                   class="control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.user.phone_number')</label>


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

                    <div class="col-sm-12">
                        <h3>@lang('woningdossier.cooperation.my-account.settings.index.header-password')</h3>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group{{ $errors->has('user.current_password') ? ' has-error' : '' }}">
                            <label for="current_password"
                                   class="control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.user.current-password')</label>


                            <input id="current_password" type="password" class="form-control"
                                   name="user[current_password]">

                            @if ($errors->has('user.current_password'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('user.current_password') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group{{ $errors->has('user.password') ? ' has-error' : '' }}">
                            <label for="password"
                                   class="control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.user.new-password')</label>


                            <input id="password" type="password" class="form-control" name="user[password]">

                            @if ($errors->has('user.password'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('user.password') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="password-confirm"
                                   class="control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.user.new-password-confirmation')</label>


                            <input id="password-confirm" type="password" class="form-control"
                                   name="user[password_confirmation]">
                        </div>
                    </div>

                </div>
                <!-- password change section -->

                <div class="row">
                    <div class="col-sm-12">
                        <h3>@lang('woningdossier.cooperation.my-account.settings.index.header-building')</h3>
                    </div>
                    <div class="col-sm-4">

                        <div class="form-group{{ $errors->has('building.postal_code') ? ' has-error' : '' }}">
                            <label for="building.postal_code" class="control-label">
                                @lang('woningdossier.cooperation.my-account.settings.index.form.building.postal-code')
                            </label>
                            <input type="text" class="form-control" name="building[postal_code]" id="postal_code"
                                   value="{{ old('building.postal_code', $building->postal_code) }}" required autofocus>

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
                                   class="control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.building.number')</label>

                            <input type="text" class="form-control" name="building[house_number]" id="number"
                                   value="{{ old('building.house_number', $building->number) }}" required autofocus>

                            @if ($errors->has('building.house_number'))
                                <span class="help-block">
                                                <strong>{{ $errors->first('building.house_number') }}</strong>
                                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group{{ $errors->has('building.house_number_extension') ? ' has-error' : '' }}">
                            <label for="building.house_number_extension" class="control-label">
                                @lang('woningdossier.cooperation.my-account.settings.index.form.building.extension')
                            </label>

                            <input type="text" class="form-control" name="building[house_number_extension]"
                                   id="house_number_extension"
                                   value="{{ old('building.house_number_extension', $building->extension) }}" autofocus>

                            @if ($errors->has('building.house_number_extension'))
                                <span class="help-block">
                                                <strong>{{ $errors->first('building.house_number_extension') }}</strong>
                                            </span>
                            @endif
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-sm-6">


                        <div class="form-group{{ $errors->has('building.street') ? ' has-error' : '' }}">
                            <label for="street"
                                   class="control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.building.street')</label>

                            <input type="text" class="form-control" name="building[street]" id="street"
                                   value="{{ old('building.street', $building->street) }}" required autofocus>

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
                                   class="control-label">@lang('woningdossier.cooperation.my-account.settings.index.form.building.city')</label>

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
                                @lang('woningdossier.cooperation.my-account.settings.index.form.submit')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>


    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.my-account.settings.reset-file.header')</div>

        <div class="panel-body">
            @lang('woningdossier.cooperation.my-account.settings.reset-file.description')
            <form class="form-horizontal" method="POST"
                  action="{{ route('cooperation.my-account.settings.reset-file', ['cooperation' => $cooperation]) }}">
                {{ csrf_field() }}

                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="reset-file"
                               class="control-label">@lang('woningdossier.cooperation.my-account.settings.reset-file.label')</label>

                        <a id="reset-account" class="btn btn-danger">
                            @lang('woningdossier.cooperation.my-account.settings.reset-file.submit')
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @can('delete-own-account')
        <div class="panel panel-default">
            <div class="panel-heading">@lang('woningdossier.cooperation.my-account.settings.destroy.header')</div>

            <div class="panel-body">
                <form method="POST"
                      action="{{ route('cooperation.my-account.settings.destroy', ['cooperation' => $cooperation]) }}">
                    {{ method_field('DELETE') }}
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="col-sm-12">


                            <div class="form-group">
                                <label for="delete-account"
                                       class="control-label">@lang('woningdossier.cooperation.my-account.settings.destroy.label')</label>

                                <button type="submit" id="delete-account" class="btn btn-danger">
                                    @lang('woningdossier.cooperation.my-account.settings.destroy.submit')
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endcan


@endsection


@push('js')
    <script>
        var areYouSure = '@lang('woningdossier.cooperation.my-account.settings.reset-file.are-you-sure')';
        $('#reset-account').click(function () {
            if (confirm(areYouSure)) {
                $(this).closest('form').submit();
            } else {
                return false;
                event.preventDefault();
            }
        });

        var userCooperationCount = {{Auth::user()->cooperations()->count()}};

        var areYouSureToDestroy = '@lang('woningdossier.cooperation.my-account.settings.form.destroy.are-you-sure.delete-from-cooperation')';

        if (userCooperationCount === 1) {
            areYouSureToDestroy = '@lang('woningdossier.cooperation.my-account.settings.destroy.are-you-sure.complete-delete')';
        }

        $('#delete-account').click(function () {
            if (confirm(areYouSureToDestroy)) {
                $(this).closest('form').submit();
            } else {
                event.preventDefault();
                return false;
            }
        })
    </script>
@endpush