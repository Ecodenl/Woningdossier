@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/buildings.show.header', [
                'name' => $user->getFullName(),
                'street-and-number' => $building->street.' '.$building->number.' '.$building->extension,
                'zipcode-and-city' => $building->postal_code.' '.$building->city,
                'email' => $user->account->email,
                'phone-number' => $user->phone_number
            ])
        </div>


        <div class="panel-body">
            {{-- delete a user --}}
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{route('cooperation.admin.buildings.update', compact('building'))}}" class="has-address-data" method="POST">
                        {{csrf_field()}}
                        {{method_field('PUT')}}

                        <h3>@lang('cooperation/admin/buildings.edit.account-user-info-title')</h3>
                        <div class="row">
                            <div class="col-md-6 col-lg-4">
                                @component('layouts.parts.components.form-group', ['input_name' => 'users.first_name'])
                                    <label for="first-name" class="control-label">@lang('users.column-translations.first_name')</label>


                                    <input id="fist-name" type="text" class="form-control" name="users[first_name]" value="{{ old('users.first_name', $user->first_name) }}" required autofocus>
                                @endcomponent
                            </div>
                          
                            <div class="col-md-6 col-lg-4">
                                @component('layouts.parts.components.form-group', ['input_name' => 'users.last_name'])
                                    <label for="last-name" class="control-label">@lang('users.column-translations.last_name')</label>


                                    <input id="last-name" type="text" class="form-control" name="users[last_name]" value="{{ old('users.last_name', $user->last_name) }}" required>
                                @endcomponent
                            </div>
                          
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-lg-4">
                                @component('layouts.parts.components.form-group', ['input_name' => 'accounts.email'])
                                    <label for="email" class="control-label">@lang('accounts.column-translations.email')</label>


                                    <input id="email" type="email" class="form-control" name="accounts[email]" value="{{ old('account.email', $account->email) }}" required>
                                @endcomponent
                            </div>
                            <div class="col-md-6 col-lg-4">
                                @component('layouts.parts.components.form-group', ['input_name' => 'users.phone_number'])
                                    <label for="phone-number" class="control-label">@lang('users.column-translations.phone_number')</label>


                                    <input id="phone-number" type="text" class="form-control" name="users[phone_number]" value="{{ old('users.phone_number', $user->phone_number) }}">
                                @endcomponent
                            </div>
                        </div>

                        <h3>@lang('cooperation/admin/buildings.edit.address-info-title')</h3>
                        <div class="row">
                            <div class="col-md-6 col-lg-4">
                                @component('layouts.parts.components.form-group', ['input_name' => 'buildings.postal_code'])
                                    <label class="control-label">@lang('buildings.column-translations.postal_code')</label>


                                    <input id="postal_code" type="text" class="form-control" name="buildings[postal_code]" value="{{ old('buildings.postal_code', $building->postal_code) }}" required >
                                @endcomponent

                            </div>
                            
                            <div class="col-md-6 col-lg-2">
                                @component('layouts.parts.components.form-group', ['input_name' => 'buildings.number'])
                                    <label class="control-label">@lang('buildings.column-translations.number')</label>
                                
                                    <input id="number" type="text" class="form-control" name="buildings[number]" value="{{ old('buildings.number', $building->number) }}" required>
                                @endcomponent
                            </div>    
                            <div class="col-md-6 col-lg-2">
                                @component('layouts.parts.components.form-group', ['input_name' => 'buildings.extension'])
                                    <label class="control-label">@lang('buildings.column-translations.extension')</label>
                                
                                    <input id="house_number_extension" type="text" class="form-control" name="buildings[extension]" value="{{ old('buildings.extension', $building->extension) }}">
                                @endcomponent
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 col-lg-4">
                                @component('layouts.parts.components.form-group', ['input_name' => 'buildings.city'])
                                    <label class="control-label">@lang('buildings.column-translations.city')</label>


                                    <input id="city" type="text" class="form-control" name="buildings[city]" value="{{ old('buildings.city', $building->city) }}" required>
                                @endcomponent
                            </div>
                            <div class="col-md-6 col-lg-4">
                                @component('layouts.parts.components.form-group', ['input_name' => 'buildings.street'])
                                    <label class="control-label">@lang('buildings.column-translations.street')</label>

                                    <input id="street" type="text" class="form-control" name="buildings[street]" value="{{ old('buildings.street', $building->street) }}" required >
                                @endcomponent
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">@lang('cooperation/admin/buildings.edit.form.submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
