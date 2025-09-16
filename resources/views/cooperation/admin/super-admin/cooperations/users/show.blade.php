@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.show.header', ['name' => $user->getFullName()])
])

@section('content')
    <div class="flex flex-wrap w-full">
        @component('cooperation.frontend.layouts.components.form-group', [
            'class' => 'w-full sm:w-1/2 sm:pr-3',
            'label' => __('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.show.role.label'),
            'id' => 'role-select',
            'inputName' => "user.roles",
            'withInputSource' => false,
        ])
            @component('cooperation.frontend.layouts.components.alpine-select', ['withSearch' => true])
                <select multiple id="role-select" class="form-input hidden" name="user[roles]"
                        @cannot('editAny', $userCurrentRole) disabled @endcannot>
                    @foreach($roles as $role)
                        @can('view', [$role, Hoomdossier::user(), HoomdossierSession::getRole(true)])
                            <option value="{{$role->id}}"
                                    @cannot('delete', [$role, Hoomdossier::user(), HoomdossierSession::getRole(true), $user]) readonly
                                    @endcannot
                                    @if($user->hasRole($role)) selected @endif
                            >
                                {{$role->human_readable_name}}
                            </option>
                        @endcan
                    @endforeach
                </select>
            @endcomponent
        @endcomponent
        @if(! $user->account->hasVerifiedEmail())
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'w-full sm:w-1/2 sm:pl-3',
                'label' => 'Dit account is nog niet bevestigd',
                'id' => 'confirm-account',
                'inputName' => "confirm",
                'withInputSource' => false,
            ])
                <form action="{{ route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.confirm', ['cooperationToManage' => $cooperationToManage, 'id' => $user->account->id]) }}"
                      method="POST">
                    @csrf

                    <button type="submit" class="btn btn-purple">
                        Nu bevestigen
                    </button>
                </form>
            @endcomponent
        @endif
    </div>
@endsection

@push('js')
    <script type="module" nonce="{{ $cspNonce }}">
        const userId = @js($user->id);
        const cooperationId = @js($cooperationToManage->id);

        function performFetch(url, body, redirect) {
            fetchRequest(url, 'POST', body)
                .then((response) => redirect ? location.href = redirect : location.reload());
        }

        document.addEventListener('DOMContentLoaded', function () {
            const roleSelect = document.getElementById('role-select');
            const currentRoles = Array.from(roleSelect.selectedOptions).map((option) => option.value);

            @can('editAny', $userCurrentRole)
            // User roles
            roleSelect.addEventListener('change', function (event) {
                // If length is greater, a value was removed, otherwise added
                if (currentRoles.length > this.selectedOptions.length) {
                    let removedOption = null;
                    const currentOptions = Array.from(this.selectedOptions).map((option) => option.value);
                    currentRoles.forEach((value) => removedOption = ! removedOption && ! currentOptions.includes(value) ? value : removedOption);

                    if (confirm('@lang('woningdossier.cooperation.admin.users.show.remove-role')')) {
                        performFetch('{{ route('cooperation.admin.roles.remove-role') }}', {
                            role_id: removedOption,
                            user_id: userId,
                            cooperation_id: cooperationId
                        });
                    } else {
                        this.alpineSelect.updateValue(removedOption);
                    }
                } else {
                    let newOption = null;
                    Array.from(this.selectedOptions).forEach((option) => newOption = ! newOption && ! currentRoles.includes(option.value) ? option.value : newOption);

                    if (confirm('@lang('woningdossier.cooperation.admin.users.show.give-role')')) {
                        performFetch('{{ route('cooperation.admin.roles.assign-role') }}', {
                            role_id: newOption,
                            user_id: userId,
                        });
                    } else {
                        this.alpineSelect.updateValue(newOption);
                    }
                }
            });
            @endcan
        });
    </script>
@endpush
