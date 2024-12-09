@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.add-user')
])

@section('content')
    <div class="panel panel-default">
        <div class="panel-body" x-data>
            @component('cooperation.tool.components.alert', [
                'alertType' => 'info',
                'dismissible' => false,
                'attr' => 'style="display: none;" x-on:duplicates-checked.window="$el.style.display = $event.detail.showDuplicateError ? \'\' : \'none\'"',
            ])
                @lang('auth.register.form.duplicate-address')
            @endcomponent
            <div class="row"
                 x-data="register('{{route('cooperation.check-existing-email', ['cooperation' => $cooperation, 'forCooperation' => $cooperationToManage ?? $cooperation])}}')">
                <form class="has-address-data col-sm-12"
                      @if(isset($cooperationToManage))
                          action="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.store', compact('cooperation', 'cooperationToManage'))}}"
                      @else
                          action="{{route('cooperation.admin.users.store', compact('cooperation'))}}"
                      @endif
                      method="post">
                    @csrf

                    <h3>@lang('cooperation/admin/buildings.edit.account-user-info-title')</h3>
                    <div class="row">
                        <div class="col-md-6 col-lg-4" x-show="! alreadyMember">
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

                        <div class="col-md-6 col-lg-4" x-show="! alreadyMember">
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
                                       value="{{ old('accounts.email') }}" required x-on:change="checkEmail($el)">
                                <p class="text-info" x-show="alreadyMember" x-cloak>
                                    @lang('cooperation/admin/users.create.form.already-member')
                                </p>
                                <p class="text-info" x-show="emailExists" x-cloak>
                                    @lang('cooperation/admin/users.create.form.e-mail-exists')
                                </p>
                            @endcomponent
                        </div>
                        <div class="col-md-6 col-lg-4" x-show="! alreadyMember">
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
                        <div class="col-md-6 col-lg-4" x-show="! alreadyMember">
                            @component('layouts.parts.components.form-group', [
                                       'input_name' => 'roles',
                                    ])
                                <label for="roles">@lang('cooperation/admin/users.create.form.roles')</label>
                                <select @cannot('editAny',$userCurrentRole) disabled="disabled" @endcannot class="form-control roles" name="roles[]"
                                       id="role-select" multiple="multiple">
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

                    <div class="row">
                        <div class="col-md-6 col-lg-4" x-show="! alreadyMember">
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

                    <h3 x-show="! alreadyMember">@lang('cooperation/admin/buildings.edit.address-info-title')</h3>
                    <div class="row">
                        <div class="col-xs-8" x-show="! alreadyMember">
                            @include('cooperation.layouts.address-bootstrap', [
                                'withLabels' => true,
                                'checks' => [
                                    'correct_address', 'duplicates',
                                ],
                            ])
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 save-button" x-show="! alreadyMember">
                            <button class="btn btn-primary btn-block" x-bind:disabled="alreadyMember"
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
    <script type="module">
        document.addEventListener('DOMContentLoaded', function () {
            let oldSelectedRoles = [];

            $(".roles").select2({
                placeholder: "@lang('cooperation/admin/users.create.form.select-role')",
                maximumSelectionLength: Infinity
            }).select2('val', oldSelectedRoles);

            $(".coach").select2({
                placeholder: "@lang('cooperation/admin/users.create.form.select-coach')",
                maximumSelectionLength: Infinity,
                allowClear: true
            }).val(null).trigger("change");
        });
    </script>
@endpush