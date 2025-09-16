@if(! Hoomdossier::user()->isFillingToolForOtherBuilding() && Hoomdossier::user()->getRoleNames()->count() > 1 && \App\Helpers\HoomdossierSession::hasRole())
    @component('cooperation.layouts.components.dropdown', [
        'label' => __('cooperation/frontend/layouts.navbar.current-role') . \Spatie\Permission\Models\Role::find(\App\Helpers\HoomdossierSession::getRole())->human_readable_name,
        'class' => 'in-text',
        ])
        @foreach(Hoomdossier::user()->roles()->orderBy('level', 'DESC')->get() as $role)
            <li>
                <a href="{{ route('cooperation.admin.switch-role', ['role' => $role->name]) }}"
                   class="in-text">
                    {{ $role->human_readable_name }}
                </a>
            </li>
        @endforeach
    @endcomponent
@endif