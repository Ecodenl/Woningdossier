@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.add-user')</div>

        <div class="panel-body">
            <div class="row">
                <form class="has-address-data"
                      @if(isset($cooperationToManage))
                          action="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.store', compact('cooperation', 'cooperationToManage'))}}"
                      @else
                          action="{{route('cooperation.admin.users.store', compact('cooperation'))}}"
                      @endif
                      method="post">
                    @csrf
                    <input id="addressid" name="addressid" type="text" value="" style="display:none;">

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

                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-12">
                                @component('layouts.parts.components.form-group', [
                                    'input_name' => 'email',
                                ])
                                    <label for="email">@lang('cooperation/admin/users.create.form.email')</label>
                                    <input id="email" type="email" value="{{old('email')}}" class="form-control"
                                           placeholder="@lang('cooperation/admin/users.create.form.email')..."
                                           name="email">
                                @endcomponent
                            </div>
                        </div>

                        <div class="user-info">
                            <div class="row">
                                <div class="col-sm-12">
                                    @component('layouts.parts.components.form-group', [
                                       'input_name' => 'first_name',
                                    ])
                                        <label for="first-name">@lang('cooperation/admin/users.create.form.first-name')</label>
                                        <input value="{{old('first_name')}}" type="text" class="form-control"
                                               name="first_name"
                                               placeholder="@lang('cooperation/admin/users.create.form.first-name')...">
                                    @endcomponent
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    @component('layouts.parts.components.form-group', [
                                       'input_name' => 'last_name',
                                    ])
                                        <label for="last_name">@lang('cooperation/admin/users.create.form.last-name')</label>
                                        <input value="{{old('last_name')}}" type="text" class="form-control"
                                               placeholder="@lang('cooperation/admin/users.create.form.last-name')..."
                                               name="last_name">
                                    @endcomponent
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    @component('layouts.parts.components.form-group', [
                                       'input_name' => 'roles',
                                    ])
                                        <label for="roles">@lang('cooperation/admin/users.create.form.roles')</label>
                                        <select @cannot('editAny',$userCurrentRole) disabled="disabled" @endcannot class="form-control roles" name="roles[]" id="role-select" multiple="multiple">
                                            @foreach($roles as $role)
                                                @can('view', [$role, Hoomdossier::user(), $userCurrentRole])
                                                    <option value="{{$role->id}}" @if(in_array($role->id, old('roles', []))) selected="selected" @endif>
                                                        {{$role->human_readable_name}}
                                                    </option>
                                                @endcan
                                            @endforeach
                                        </select>
                                    @endcomponent
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="resident-info" class="col-md-6">
                        <div class="row">
                            <div class="form-group{{ $errors->has('postal_code') || $errors->has('number') ? ' has-error' : '' }}">
                                <div class="col-md-4">
                                    <label for="postal_code"
                                           class="">@lang('cooperation/admin/users.create.form.postal-code')
                                        <span class="">*</span></label>

                                    <input id="postal_code" type="text" class="form-control" name="postal_code"
                                           value="{{old('postal_code')}}">

                                </div>
                                <div class="col-md-4">
                                    <label for="number"
                                           class="">@lang('cooperation/admin/users.create.form.number')
                                        <span class="">*</span></label>

                                    <input id="number" type="text" class="form-control" name="number"
                                           value="{{ old('number') }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="house_number_extension"
                                           class="">@lang('cooperation/admin/users.create.form.house-number-extension')</label>
                                    <input id="house_number_extension" type="text" class="form-control"
                                           name="house_number_extension"
                                           placeholder="@lang('cooperation/admin/users.create.form.house-number-extension')"
                                           value="{{ old('house_number_extension') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                @component('layouts.parts.components.form-group', [
                                   'input_name' => 'street',
                                ])
                                    <label for="street"
                                           class="">@lang('cooperation/admin/users.create.form.street')
                                        <span class="">*</span>
                                    </label>

                                    <input id="street" type="text" class="form-control" name="street"
                                           value="{{ old('street') }}">
                                @endcomponent
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                @component('layouts.parts.components.form-group', [
                                   'input_name' => 'city',
                                ])
                                    <label for="city"
                                           class="">@lang('cooperation/admin/users.create.form.city')
                                        <span class="">*</span></label>

                                    <input id="city" type="text" class="form-control" name="city"
                                           value="{{ old('city') }}">
                                @endcomponent
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                @component('layouts.parts.components.form-group', [
                                   'input_name' => 'phone_number',
                                ])
                                    <label for="phone_number" class="">
                                        @lang('cooperation/admin/users.create.form.phone-number')
                                    </label>

                                    <input id="phone_number" type="text" class="form-control" name="phone_number"
                                           value="{{ old('phone_number') }}">
                                @endcomponent
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
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
                    </div>
                    <div class="col-sm-12 save-button">
                        <button class="btn btn-primary btn-block"
                                type="submit">@lang('cooperation/admin/users.create.form.submit')
                            <span class="glyphicon glyphicon-plus"></span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {

            var oldSelectedRoles = [];

            $(".roles").select2({
                placeholder: "@lang('cooperation/admin/users.create.form.select-role')",
                maximumSelectionLength: Infinity
            }).select2('val', oldSelectedRoles);

            $(".coach").select2({
                placeholder: "@lang('cooperation/admin/users.create.form.select-coach')",
                maximumSelectionLength: Infinity,
                allowClear: true
            }).val(null).trigger("change");


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