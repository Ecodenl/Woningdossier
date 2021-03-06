@extends('cooperation.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('auth.register.form.header')</div>

                    <div class="panel-body">
                        <form class="form-horizontal has-address-data" method="POST" id="register"
                              action="{{ route('cooperation.register', ['cooperation' => $cooperation]) }}">
                            {{ csrf_field() }}
                            <input id="addressid" name="addressid" type="text" value="" style="display:none;">

                            <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
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

                            <div class="form-group" id="email-is-already-registered" style="display: none;">
                                <div class="col-md-offset-4 col-md-8">
                                    @component('cooperation.tool.components.alert', ['alertType' => 'info'])
                                        <div id="is-already-member">
                                            @lang('auth.register.form.already-member')
                                        </div>
                                        <div class="email-exist">
                                            @lang('auth.register.form.e-mail-exists')
                                        </div>
                                    @endcomponent
                                </div>
                            </div>
                            <div class="user-info">
                                <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                                    <label for="first_name"
                                           class="col-md-4 control-label">@lang('auth.register.form.first_name')<span
                                                class="required">*</span></label>

                                    <div class="col-md-8">
                                        <input id="first_name" type="text" class="form-control" name="first_name"
                                               value="{{ old('first_name') }}" required>

                                        @if ($errors->has('first_name'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('first_name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                                    <label for="last_name"
                                           class="col-md-4 control-label">@lang('auth.register.form.last_name')<span
                                                class="required">*</span></label>

                                    <div class="col-md-8">
                                        <input id="last_name" type="text" class="form-control" name="last_name"
                                               value="{{ old('last_name') }}" required>

                                        @if ($errors->has('last_name'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('last_name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-md-6">

                                        <div class="form-group row {{ $errors->has('postal_code') ? ' has-error' : '' }}">
                                            <label for="postal_code"
                                                   class="col-md-8 control-label">@lang('auth.register.form.postal_code')
                                                <span class="required">*</span></label>

                                            <div class="col-md-4">
                                                <input id="postal_code" type="text" class="form-control"
                                                       name="postal_code" value="{{ old('postal_code') }}" required>
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
                                                <label for="number"
                                                       class="control-label">@lang('auth.register.form.number')<span
                                                            class="required">*</span></label>
                                            </div>

                                            <div class="col-md-6">
                                                <input id="number" type="text" class="form-control" name="number"
                                                       value="{{ old('number') }}" required>
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
                                                <input id="house_number_extension" type="text" class="form-control"
                                                       name="house_number_extension"
                                                       placeholder="@lang('auth.register.form.house_number_extension')"
                                                       value="{{ old('house_number_extension') }}">
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
                                    <label for="street"
                                           class="col-md-4 control-label">@lang('auth.register.form.street')<span
                                                class="required">*</span></label>

                                    <div class="col-md-8">
                                        <input id="street" type="text" class="form-control" name="street"
                                               value="{{ old('street') }}" required>

                                        @if ($errors->has('street'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('street') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('city') ? ' has-error' : '' }}">
                                    <label for="city" class="col-md-4 control-label">@lang('auth.register.form.city')
                                        <span class="required">*</span></label>

                                    <div class="col-md-8">
                                        <input id="city" type="text" class="form-control" name="city"
                                               value="{{ old('city') }}" required>

                                        @if ($errors->has('city'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('city') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('phone_number') ? ' has-error' : '' }}">
                                    <label for="phone_number"
                                           class="col-md-4 control-label">@lang('auth.register.form.phone_number')</label>

                                    <div class="col-md-8">
                                        <input id="phone_number" type="text" class="form-control" name="phone_number"
                                               value="{{ old('phone_number') }}">

                                        @if ($errors->has('phone_number'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('phone_number') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} account-info">
                                    <label for="password"
                                           class="col-md-4 control-label">@lang('auth.register.form.password')<span
                                                class="required">*</span></label>

                                    <div class="col-md-8">
                                        <input id="password" type="password" class="form-control" name="password"
                                               required>

                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group account-info">
                                    <label for="password-confirm"
                                           class="col-md-4 control-label">@lang('auth.register.form.password_confirmation')
                                        <span class="required">*</span></label>

                                    <div class="col-md-8">
                                        <input id="password-confirm" type="password" class="form-control"
                                               name="password_confirmation" required>
                                    </div>
                                </div>

                                <?php $allowAccessChecked = old('allow_access') == 'on'; ?>

                                <div class="form-group {{ $errors->has('allow_access') ? ' has-error' : '' }}">
                                    <div class="col-md-push-4 col-md-8">
                                        <label for="allow_access">
                                            <input id="allow_access" name="allow_access" type="checkbox" @if($allowAccessChecked)checked="checked"@endif>
                                            @lang('conversation-requests.index.form.allow_access', ['cooperation' => \App\Helpers\HoomdossierSession::getCooperation(true)->name])
                                        </label>
                                        @if ($errors->has('allow_access'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('allow_access') }}</strong>
                                        </span>
                                        @endif
                                        <p>@lang('conversation-requests.index.text')</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-8 col-md-offset-4">
                                        <button data-toggle="tooltip" type="submit" class="btn btn-primary @if(!$allowAccessChecked) disabled @endif"
                                                @if(!$allowAccessChecked) disabled="disabled" @endif title="@lang('auth.register.form.allow-access')">
                                            @lang('auth.register.form.button')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {

            var submitButton = $('button[type="submit"]');
            var email = $('#email');
            submitButton.tooltip();

            $('#allow_access').change(function () {
                if ($(this).is(':checked')) {
                    submitButton.prop('disabled', false).removeClass('disabled');
                    submitButton.tooltip('disable');
                } else {
                    submitButton.prop('disabled', true).addClass('disabled');
                    submitButton.tooltip('enable');
                }
            });

            // prevent double clicked submit buttons
            $('form').on('submit', function () {
                submitButton.prop('disabled', 'disabled').addClass('disabled');
            });


            email.on('change', function () {
                if ($(this).val().length > 0) {
                    checkEmailForTypo($(this));
                }
            });
            email.on('keyup change', function () {
                $.ajax({
                    url: '{{route('cooperation.check-existing-email')}}',
                    method: "GET",
                    data: {email: $(this).val()},
                }).done(function (data) {
                    var emailIsAlreadyRegistered = $('#email-is-already-registered');

                    var passwordInput = $('#password');
                    var passwordConfirmInput = $('#password-confirm');
                    var emailInput = $('#email');

                    // email exists
                    if (data.email_exists) {
                        var isAlreadyMemberMessage = $('#is-already-member');
                        var emailExistsDiv = $('.email-exist');

                        emailIsAlreadyRegistered.show();

                        emailInput.prop('required', false);
                        passwordInput.prop('required', false);
                        passwordConfirmInput.prop('required', false);

                        // check if the email is connected to the current cooperation
                        // and show the matching messages
                        if (data.user_is_already_member_of_cooperation) {
                            // hide the account stuff
                            isAlreadyMemberMessage.show();
                            emailExistsDiv.hide();
                            $('.user-info').hide();
                        } else {
                            $('.account-info').hide();
                            isAlreadyMemberMessage.hide();
                            emailExistsDiv.show();
                        }

                    } else {
                        emailIsAlreadyRegistered.hide();
                        $('.user-info').show();
                        $('.account-info').show();
                        emailInput.prop('required', true);
                        passwordInput.prop('required', true);
                        passwordConfirmInput.prop('required', true);
                    }
                });

            });
            if ($('.form-error').length) {
                email.trigger('change');
            }
        });

        function addError(input, message) {
            var helpBlock = '<span class="help-block"></span>';
            input.parent().parent().addClass('has-error');
            input.parent().append($(helpBlock).append('<strong>' + message + '</strong>'));
        }

        function removeError(input) {
            input.parents('.has-error').removeClass('has-error');
            input.next('.help-block').remove()
        }
        function checkEmailForTypo(emailElement) {
            var emailWarning = '{{__('auth.register.form.possible-wrong-email')}}';
            var goodDomains = /\b(nl|be|net|com|info|nu|de)\b/;

            // if the email does not contain a good domain return a message
            if (!goodDomains.test(emailElement.val())) {
                removeError(emailElement);
                addError(emailElement, emailWarning)
            } else {
                removeError(emailElement);
            }
        }

    </script>
@endpush
