@extends('cooperation.layouts.app')

@section('content')
    <div class="container">
        <form class="form-horizontal" method="POST" action="{{ route('cooperation.create-building.store', ['cooperation' => $cooperation]) }}" autocomplete="off">
            {{csrf_field()}}
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default">
                        <div class="panel-heading">@lang('woningdossier.cooperation.create-building.current-login-info.header')</div>

                        <div class="panel-body">
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">@lang('auth.register.form.e-mail')
                                    <span class="required">*</span></label>

                                <div class="col-md-8">
                                    <input id="email" type="email" class="form-control" name="email"
                                           value="{{ old('email') }}" required autofocus>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password"
                                       class="col-md-4 control-label">@lang('auth.register.form.password')<span
                                            class="required">*</span></label>

                                <div class="col-md-8">
                                    <input id="password" type="password" class="form-control" name="password" required>

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
            <div class="row has-address-data">

                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default">
                        <div class="panel-heading">@lang('woningdossier.cooperation.create-building.building.header')</div>

                        <div class="panel-body">
                            <input id="addressid" name="addressid" type="text" value="" style="display:none;">


                            <div id="other-form-data">
                                <div class="row">

                                    <div class="col-md-6">

                                        <div class="form-group row {{ $errors->has('postal_code') ? ' has-error' : '' }}">
                                            <label for="postal_code" class="col-md-8 control-label">@lang('auth.register.form.postal_code')<span class="required">*</span></label>

                                            <div class="col-md-4">
                                                <input id="postal_code" type="text" class="form-control" name="postal_code" value="{{ old('postal_code') }}" required >
                                            </div>

                                            @if ($errors->has('postal_code'))
                                                <span class="help-block">
                                                <strong>{{ $errors->first('postal_code') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="row form-group{{ $errors->has('number') ? ' has-error' : '' }}">
                                            <div class="col-md-6">
                                                <label for="number" class="control-label">@lang('auth.register.form.number')<span class="required">*</span></label>
                                            </div>

                                            <div class="col-md-6">
                                                <input id="number" type="text" class="form-control" name="number" value="{{ old('number') }}" required >
                                            </div>

                                            @if ($errors->has('number'))
                                                <span class="help-block">
                                                <strong>{{ $errors->first('number') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group row {{ $errors->has('house_number_extension') ? ' has-error' : '' }}">
                                            <div class="col-sm-12">
                                                <input id="house_number_extension" type="text" class="form-control" name="house_number_extension" placeholder="@lang('auth.register.form.house_number_extension')" value="{{ old('house_number_extension') }}" >
                                            </div>

                                            @if ($errors->has('house_number_extension'))
                                                <span class="help-block">
                                                <strong>{{ $errors->first('house_number_extension') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group d-none" id="possible-wrong-postal-code">
                                            <div class="col-md-offset-4 col-md-8">
                                                @component('cooperation.tool.components.alert', ['alertType' => 'info'])
                                                    @lang('auth.register.form.possible-wrong-postal-code')
                                                @endcomponent
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('street') ? ' has-error' : '' }}">
                                    <label for="street" class="col-md-4 control-label">@lang('auth.register.form.street')<span class="required">*</span></label>

                                    <div class="col-md-8">
                                        <input id="street" type="text" class="form-control" name="street" value="{{ old('street') }}" required >

                                        @if ($errors->has('street'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('street') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('city') ? ' has-error' : '' }}">
                                    <label for="city" class="col-md-4 control-label">@lang('auth.register.form.city')<span class="required">*</span></label>

                                    <div class="col-md-8">
                                        <input id="city" type="text" class="form-control" name="city" value="{{ old('city') }}" required >

                                        @if ($errors->has('city'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('city') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>



                                <div class="form-group">
                                    <div class="col-md-8 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary">
                                            @lang('woningdossier.cooperation.create-building.building.store')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

