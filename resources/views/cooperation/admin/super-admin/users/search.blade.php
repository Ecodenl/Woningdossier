<form class="w-full flex flex-wrap" action="{{route('cooperation.admin.super-admin.users.index')}}" method="GET">
    <div class="w-full">
        <h3 class="heading-3">
            @lang('cooperation/admin/super-admin/users.index.form.user.title')
        </h3>
    </div>

    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'label' => __('cooperation/admin/super-admin/users.index.form.user.first-name'),
        'id' => "first-name",
        'class' => 'w-full lg:w-1/4 lg:pr-3',
        'inputName' => "users.first_name",
    ])
        <input id="first-name" type="text" name="user[first_name]"
               value="{{$userData['first_name'] ?? ''}}" class="form-input">
    @endcomponent
    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'label' => __('cooperation/admin/super-admin/users.index.form.user.last-name'),
        'id' => "last-name",
        'class' => 'w-full lg:w-1/4 lg:px-3',
        'inputName' => "users.last_name",
    ])
        <input id="last-name" type="text" name="user[last_name]"
               value="{{$userData['last_name'] ?? ''}}" class="form-input">
    @endcomponent
    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'label' => __('cooperation/admin/super-admin/users.index.form.account.email'),
        'id' => "email",
        'class' => 'w-full lg:w-1/2 lg:pl-3',
        'inputName' => "account.email",
    ])
        <input id="email" type="text" name="account[email]" value="{{$accountData['email'] ?? ''}}" class="form-input">
    @endcomponent

    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'label' => __('cooperation/admin/super-admin/users.index.form.user.role'),
        'id' => "role",
        'class' => 'w-full lg:w-1/4 lg:pr-3',
        'inputName' => "users.role_id",
    ])
        @component('cooperation.frontend.layouts.components.alpine-select')
            <select id="role" name="user[role_id]" class="form-input hidden">
                <option selected value="">---</option>
                @php $selectedRoleId = $userData['role_id'] ?? null; @endphp
                @foreach($roles as $role)
                    <option value="{{$role->id}}"
                            @if($role->id == $selectedRoleId) selected @endif>
                        {{$role->human_readable_name}}
                    </option>
                @endforeach
            </select>
        @endcomponent
    @endcomponent

    <div class="w-full mt-5">
        <h3 class="heading-3">
            @lang('cooperation/admin/super-admin/users.index.form.building.title')
        </h3>
    </div>
    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'label' => __('cooperation/admin/super-admin/users.index.form.building.postal-code'),
        'id' => "postal-code",
        'class' => 'w-full lg:w-1/4 lg:pr-3',
        'inputName' => "building.postal_code",
    ])
        <input id="postal-code" type="text" name="building[postal_code]"
               value="{{$buildingData['postal_code'] ?? ''}}" class="form-input">
    @endcomponent
    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'label' => __('cooperation/admin/super-admin/users.index.form.building.city'),
        'id' => "city",
        'class' => 'w-full lg:w-1/4 lg:px-3',
        'inputName' => "building.city",
    ])
        <input id="city" type="text" name="building[city]"
               value="{{$buildingData['city'] ?? ''}}" class="form-input">
    @endcomponent

    <div class="w-full mt-5">
        <button type="submit" class="btn btn-green">
            @lang('cooperation/admin/super-admin/users.index.form.submit')
        </button>
    </div>
</form>
