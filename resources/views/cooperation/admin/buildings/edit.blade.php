@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/buildings.show.header', [
                'name' => $user->getFullName(),
                'street-and-number' => $building->street.' '.$building->number.' '.$building->extension,
                'zipcode-and-city' => $building->postal_code.' '.$building->city,
                'municipality' => optional($building->municipality)->name ?? __('cooperation/admin/buildings.show.unknown-municipality'),
                'email' => $user->account->email,
                'phone-number' => $user->phone_number
            ])
        </div>

        <div class="panel-body">
            {{-- delete a user --}}
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{route('cooperation.admin.buildings.update', compact('building'))}}" class="has-address-data" method="POST">
                        @csrf
                        @method('PUT')

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
                                           value="{{ old('users.first_name', $user->first_name) }}" required autofocus>
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
                                           value="{{ old('users.last_name', $user->last_name) }}" required>
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
                                           value="{{ old('accounts.email', $account->email) }}" required>
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
                                           value="{{ old('users.phone_number', $user->phone_number) }}">
                                @endcomponent
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-lg-4">
                                @component('layouts.parts.components.form-group', [
                                    'input_name' => 'users.extra.contact_id',
                                ])
                                    <label for="contact-id" class="control-label">
                                        @lang('users.column-translations.contact-id')
                                    </label>
                                    <input id="contact-id" type="text" class="form-control"
                                           name="users[extra][contact_id]"
                                           value="{{ old('users.extra.contact_id', $user->extra['contact_id'] ?? '') }}">
                                @endcomponent
                            </div>
                            <div class="col-md-6 col-lg-4">
                                @component('layouts.parts.components.form-group', [
                                    'input_name' => 'accounts.id',
                                ])
                                    <label for="account-id" class="control-label">
                                        @lang('accounts.column-translations.id')
                                    </label>
                                    <input id="account-id" type="text" class="form-control" disabled
                                           name="accounts[id]"
                                           value="{{ $user->account->id }}">
                                @endcomponent
                            </div>
                        </div>
                        {{-- TODO: Roles? --}}

                        <h3>@lang('cooperation/admin/buildings.edit.address-info-title')</h3>
                        <div class="row">
                            <div class="col-xs-8">
                                @include('cooperation.layouts.address-bootstrap', [
                                    'withLabels' => true,
                                    'defaults' => $building,
                                ])
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">
                            @lang('cooperation/admin/buildings.edit.form.submit')
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
