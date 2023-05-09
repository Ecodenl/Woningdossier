@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.add-user')</div>

        <div class="panel-body">
            <div class="row">
                <form class="has-address-data col-sm-12"
                      @if(isset($cooperationToManage))
                          action="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.store', compact('cooperation', 'cooperationToManage'))}}"
                      @else
                          action="{{route('cooperation.admin.users.store', compact('cooperation'))}}"
                      @endif
                      method="post">
                    @csrf

                    <div class="col-md-12">
                        <div class="row">
                            <div class="form-group" id="email-is-already-registered" style="display: none;">
                                <div class="col-md-12">
                                    @component('cooperation.tool.components.alert', ['alertType' => 'info'])
                                        <div id="is-already-member">
                                            @lang('cooperation/admin/users.create.form.already-member')
                                        </div>
                                        <div class="email-exist">
                                            @lang('cooperation/admin/users.create.form.e-mail-exists')
                                        </div>
                                    @endcomponent
                                </div>
                            </div>
                        </div>
                    </div>

                    <h3>@lang('cooperation/admin/buildings.edit.account-user-info-title')</h3>
                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            @component('layouts.parts.components.form-group', [
                                'input_name' => 'users.first_name'
                            ])
                                <label for="first-name" class="control-label">
                                    @lang('users.column-translations.first_name')
                                </label>
                                <input id="first-name" type="text" class="form-control" name="users[first_name]"
                                       value="{{ old('users.first_name') }}" required autofocus>
                            @endcomponent
                        </div>

                        <div class="col-md-6 col-lg-4">
                            @component('layouts.parts.components.form-group', [
                                'input_name' => 'users.last_name'
                            ])
                                <label for="last-name" class="control-label">
                                    @lang('users.column-translations.last_name')
                                </label>
                                <input id="last-name" type="text" class="form-control" name="users[last_name]"
                                       value="{{ old('users.last_name') }}" required>
                            @endcomponent
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            @component('layouts.parts.components.form-group', [
                                'input_name' => 'accounts.email'
                            ])
                                <label for="email" class="control-label">
                                    @lang('accounts.column-translations.email')
                                </label>
                                <input id="email" type="email" class="form-control" name="accounts[email]"
                                       value="{{ old('accounts.email') }}" required>
                            @endcomponent
                        </div>
                        <div class="col-md-6 col-lg-4">
                            @component('layouts.parts.components.form-group', [
                                'input_name' => 'users.phone_number'
                            ])
                                <label for="phone-number" class="control-label">
                                    @lang('users.column-translations.phone_number')
                                </label>
                                <input id="phone-number" type="text" class="form-control" name="users[phone_number]"
                                       value="{{ old('users.phone_number') }}">
                            @endcomponent
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            @component('layouts.parts.components.form-group', [
                                       'input_name' => 'roles',
                                    ])
                                <label for="roles">@lang('cooperation/admin/users.create.form.roles')</label>
                                <select name="roles[]" class="roles form-control" id="roles"
                                        multiple="multiple">
                                    @foreach($roles as $role)
                                        <option value="{{$role->id}}">
                                            {{$role->human_readable_name}}
                                        </option>
                                    @endforeach
                                </select>
                            @endcomponent
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            @component('layouts.parts.components.form-group', [
                               'input_name' => 'coach_id',
                            ])
                                <label for="coach">@lang('cooperation/admin/users.create.form.select-coach')</label>
                                <select name="coach_id" class="coach form-control" id="coach">
                                    @foreach($coaches as $coach)
                                        <option value="{{$coach->id}}"
                                                @if(old('coach_id') == $coach->id) selected @endif>
                                            {{$coach->getFullName()}}
                                        </option>
                                    @endforeach
                                </select>
                            @endcomponent
                        </div>
                    </div>
                    {{-- TODO: Contact ID? --}}

                    <h3>@lang('cooperation/admin/buildings.edit.address-info-title')</h3>
                    <div class="row">
                        <div class="col-xs-8">
                            @include('cooperation.layouts.address-bootstrap', [
                                'withLabels' => true,
                            ])
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 save-button">
                            <button class="btn btn-primary btn-block"
                                    type="submit">@lang('cooperation/admin/users.create.form.submit')
                                <span class="glyphicon glyphicon-plus"></span></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            let oldSelectedRoles = [];

            @if(! is_null(old('roles')))
            @foreach(old('roles') as $roleId)
            oldSelectedRoles.push('{{$roleId}}');
            @endforeach
            @endif

            $(".roles").select2({
                placeholder: "@lang('cooperation/admin/users.create.form.select-role')",
                maximumSelectionLength: Infinity
            }).select2('val', oldSelectedRoles);

            $(".coach").select2({
                placeholder: "@lang('cooperation/admin/users.create.form.select-coach')",
                maximumSelectionLength: Infinity,
                allowClear: true
            }).val(null).trigger("change");

            // TODO: Convert to register Alpine x-data
            var email = $('#email');

            email.on('keyup change', function () {
                $.ajax({
                    url: '{{route('cooperation.check-existing-email', ['cooperation' => $cooperation, 'forCooperation' => $cooperationToManage ?? $cooperation])}}',
                    method: "GET",
                    data: {email: $(this).val()},
                }).done(function (data) {
                    var emailIsAlreadyRegistered = $('#email-is-already-registered');

                    // email exists
                    if (data.email_exists) {
                        var isAlreadyMemberMessage = $('#is-already-member');
                        var emailExistsDiv = $('.email-exist');

                        emailIsAlreadyRegistered.show();

                        // check if the email is connected to the current cooperation
                        // and show the matching messages
                        if (data.user_is_already_member_of_cooperation) {
                            // hide the account stuff
                            isAlreadyMemberMessage.show();
                            emailExistsDiv.hide();
                            $('.user-info').hide();
                            $('#resident-info').hide();
                            $('.save-button').hide();
                        } else {
                            isAlreadyMemberMessage.hide();
                            emailExistsDiv.show();
                            $('.user-info').show();
                            $('#resident-info').show();
                            $('.save-button').show();
                        }

                    } else {
                        emailIsAlreadyRegistered.hide();
                        $('.user-info').show();
                        $('#resident-info').show();
                        $('.save-button').show();
                    }
                });
            });

            if ($('.form-error').length) {
                email.trigger('change');
            }
        });
    </script>
@endpush