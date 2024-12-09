@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/buildings.show.header', [
        'name' => $user->getFullName(),
        'street-and-number' => $building->street.' '.$building->number.' '.$building->extension,
        'zipcode-and-city' => $building->postal_code.' '.$building->city,
        'municipality' => $building->municipality?->name ?? __('cooperation/admin/buildings.show.unknown-municipality'),
        'email' => $user->account->email,
        'phone-number' => $user->phone_number,
    ])
])

@section('content')
    <div class="flex w-full xl:w-2/3"
         x-data="register('{{route('cooperation.check-existing-email', ['cooperation' => $cooperation, 'forCooperation' => $cooperationToManage ?? $cooperation])}}')">
        <form action="{{route('cooperation.admin.buildings.update', compact('building'))}}"
              class="w-full flex flex-wrap"
              method="POST">
            @csrf
            @method('PUT')

            <h3 class="w-full heading-4 mb-4">
                @lang('cooperation/admin/buildings.edit.account-user-info-title')
            </h3>

            @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'label' => __('users.column-translations.first_name'),
                    'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                    'inputName' => 'users.first_name',
                ])
                <input class="form-input" type="text" name="users[first_name]"
                       value="{{ old('users.first_name', $user->first_name) }}">
            @endcomponent
            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('users.column-translations.last_name'),
                'class' => 'w-full -mt-5 lg:w-1/2 lg:pl-3',
                'inputName' => 'users.last_name',
            ])
                <input class="form-input" type="text" name="users[last_name]"
                       value="{{ old('users.last_name', $user->last_name) }}">
            @endcomponent

            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('accounts.column-translations.email'),
                'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                'inputName' => 'accounts.email',
            ])
                <input class="form-input" type="text" name="accounts[email]"
                       value="{{ old('accounts.email', $account->email) }}"
                       x-on:change="checkEmail($el, @js($account->email));">
                <p class="text-red w-full text-left mb-2" x-show="showEmailWarning" x-cloak>
                    @lang('auth.register.form.possible-wrong-email')
                </p>
                <p class="text-red w-full text-left mb-2" x-show="alreadyMember" x-cloak>
                    @lang('cooperation/admin/users.create.form.already-member')
                </p>
                <p class="text-red w-full text-left mb-2" x-show="emailExists" x-cloak>
                    @lang('cooperation/admin/users.create.form.already-member')
                </p>
            @endcomponent
            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('users.column-translations.phone_number'),
                'class' => 'w-full -mt-5 lg:w-1/2 lg:pl-3',
                'inputName' => 'users.phone_number',
            ])
                <input class="form-input" type="text" name="users[phone_number]"
                       value="{{ old('users.phone_number', $user->phone_number) }}">
            @endcomponent

            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('users.column-translations.contact-id'),
                'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                'inputName' => 'users.extra.contact_id',
            ])
                <input id="contact-id" type="text" class="form-input"
                       name="users[extra][contact_id]"
                       value="{{ old('users.extra.contact_id', $user->extra['contact_id'] ?? '') }}">
            @endcomponent

            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('accounts.column-translations.id'),
                'class' => 'w-full -mt-5 lg:w-1/2 lg:pl-3',
                'inputName' => 'accounts.id',
            ])
                <input id="account-id" type="text" class="form-input" disabled
                       name="accounts[id]"
                       value="{{ $user->account->id }}">
            @endcomponent

            {{-- TODO: Roles? --}}

            <h3 class="w-full heading-4 my-4">
                @lang('cooperation/admin/buildings.edit.address-info-title')
            </h3>

            <div class="w-full sm:w-8/12">
                @include('cooperation.layouts.address', [
                    'withLabels' => true,
                    'defaults' => $building,
                ])
            </div>

            <div class="w-full">
                <button type="submit" class="btn btn-green">
                    @lang('cooperation/admin/buildings.edit.form.submit')
                </button>
            </div>
        </form>
    </div>
@endsection
