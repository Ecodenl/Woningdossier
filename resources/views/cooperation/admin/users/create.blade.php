@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.add-user')
])

@section('content')
    @component('cooperation.layouts.components.alert', [
        'color' => 'blue-900',
        'dismissible' => false,
        'display' => false,
        'attr' => 'style="display: none;" x-on:duplicates-checked.window="$el.style.display = $event.detail.showDuplicateError ? \'\' : \'none\'"',
    ])
        @lang('auth.register.form.duplicate-address')
    @endcomponent

    <div class="flex w-full xl:w-2/3"
         x-data="register('{{route('cooperation.check-existing-email', ['cooperation' => $cooperation, 'forCooperation' => $cooperationToManage ?? $cooperation])}}')">
        <form class="w-full flex flex-wrap"
              @if(isset($cooperationToManage))
                  action="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.store', compact('cooperation', 'cooperationToManage'))}}"
              @else
                  action="{{route('cooperation.admin.users.store', compact('cooperation'))}}"
              @endif
              method="POST">
            @csrf

            <h3 class="w-full heading-4 mb-4">
                @lang('cooperation/admin/buildings.edit.account-user-info-title')
            </h3>

            @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'label' => __('users.column-translations.first_name'),
                    'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                    'inputName' => 'users.first_name',
                    'attr' => 'x-show="! alreadyMember"',
                ])
                <input class="form-input" type="text" name="users[first_name]" value="{{ old('users.first_name') }}">
            @endcomponent
            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('users.column-translations.last_name'),
                'class' => 'w-full -mt-5 lg:w-1/2 lg:pl-3',
                'inputName' => 'users.last_name',
                'attr' => 'x-show="! alreadyMember"',
            ])
                <input class="form-input" type="text" name="users[last_name]" value="{{ old('users.last_name') }}">
            @endcomponent

            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('accounts.column-translations.email'),
                'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                'inputName' => 'accounts.email',
            ])
                <input class="form-input" type="text" name="accounts[email]" value="{{ old('accounts.email') }}"
                       x-on:change="checkEmail($el)">
                <p class="text-red w-full text-left mb-2" x-show="showEmailWarning" x-cloak>
                    @lang('auth.register.form.possible-wrong-email')
                </p>
                <p class="text-blue-800 w-full text-left mb-2" x-show="alreadyMember" x-cloak>
                    @lang('cooperation/admin/users.create.form.already-member')
                </p>
                <p class="text-blue-800 w-full text-left mb-2" x-show="emailExists" x-cloak>
                    @lang('cooperation/admin/users.create.form.e-mail-exists')
                </p>
            @endcomponent
            @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'label' => __('users.column-translations.phone_number'),
                    'class' => 'w-full -mt-5 lg:w-1/2 lg:pl-3',
                    'inputName' => 'users.phone_number',
                    'attr' => 'x-show="! alreadyMember"',
                ])
                <input class="form-input" type="text" name="users[phone_number]" value="{{ old('users.phone_number') }}">
            @endcomponent

            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                'label' => __('cooperation/admin/users.create.form.roles'),
                'id' => 'role-select',
                'inputName' => "roles",
                'withInputSource' => false,
                'attr' => 'x-show="! alreadyMember"',
            ])
                @component('cooperation.frontend.layouts.components.alpine-select', ['withSearch' => true])
                    <select multiple id="role-select" class="form-input hidden" name="roles[]"
                            @cannot('editAny', $userCurrentRole) disabled @endcannot>
                        <option selected disabled>@lang('cooperation/admin/users.create.form.select-role')</option>
                        @foreach($roles as $role)
                            @can('view', [$role, Hoomdossier::user(), HoomdossierSession::getRole(true)])
                                <option value="{{$role->id}}"
                                        @if(in_array($role->id, old('roles', []))) selected @endif
                                >
                                    {{$role->human_readable_name}}
                                </option>
                            @endcan
                        @endforeach
                    </select>
                @endcomponent
            @endcomponent

            <div class="w-full"></div>

            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                'label' => __('cooperation/admin/users.create.form.select-coach'),
                'id' => 'associated-coaches',
                'inputName' => "coach_id",
                'withInputSource' => false,
                'attr' => 'x-show="! alreadyMember"',
            ])
                @component('cooperation.frontend.layouts.components.alpine-select', ['withSearch' => true])
                    <select id="associated-coaches" class="form-input hidden" name="coach_id">
                        <option selected disabled>@lang('cooperation/admin/users.create.form.select-coach')</option>
                        @foreach($coaches as $coach)
                            <option value="{{$coach->id}}"
                                    @if(old('coach_id') == $coach->id) selected @endif
                            >
                                {{$coach->getFullName()}}
                            </option>
                        @endforeach
                    </select>
                @endcomponent
            @endcomponent

            {{-- TODO: Contact ID? --}}

            <h3 class="w-full heading-4 my-4" x-show="! alreadyMember">
                @lang('cooperation/admin/buildings.edit.address-info-title')
            </h3>

            <div class="w-full" x-show="! alreadyMember">
                @include('cooperation.layouts.address', [
                    'withLabels' => true,
                    'checks' => [
                        'correct_address', 'duplicates',
                    ],
                ])
            </div>

            <div class="w-full mt-5" x-show="! alreadyMember">
                <button class="btn btn-green flex justify-center items-center w-full" x-bind:disabled="alreadyMember" type="submit">
                    @lang('cooperation/admin/users.create.form.submit')
                    <i class="w-3 h-3 icon-plus-purple ml-1"></i>
                </button>
            </div>
        </form>
    </div>
@endsection