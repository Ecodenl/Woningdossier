<form action="{{route('cooperation.admin.super-admin.users.filter')}}" method="get">
    <div class="row">
        <div class="col-sm-12">
            <h3>@lang('admin/super-admin.users.index.form.user.title')</h3>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="">@lang('admin/super-admin.users.index.form.user.first-name')</label>
                <input type="text" name="user[first_name]" value="{{$userData['first_name'] ?? ''}}" class="form-control">
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="">@lang('admin/super-admin.users.index.form.user.last-name')</label>
                <input type="text" name="user[last_name]" value="{{$userData['last_name'] ?? ''}}" class="form-control">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="">@lang('admin/super-admin.users.index.form.account.email')</label>
                <input type="text" name="account[email]" value="{{$accountData['email'] ?? ''}}" class="form-control">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <div class="form-group">
                <label for="">@lang('admin/super-admin.users.index.form.user.role')</label>
                <select name="user[role_id]" class="form-control">
                    <?php $selectedRoleId = $userData['role_id'] ?? null; ?>
                    @foreach($roles as $role)
                        <option @if($role->id == $selectedRoleId) selected="selected" @endif value="{{$role->id}}">{{$role->human_readable_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <h3>@lang('admin/super-admin.users.index.form.building.title')</h3>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="">@lang('admin/super-admin.users.index.form.building.postal-code')</label>
                <input type="text" name="building[postal_code]" value="{{$buildingData['postal_code'] ?? ''}}" class="form-control">
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="">@lang('admin/super-admin.users.index.form.building.city')</label>
                <input type="text" name="building[city]" value="{{$buildingData['city'] ?? ''}}" class="form-control">
            </div>
        </div>

        {{--<div class="col-lg-3 col-sm-12">--}}
            {{--<div class="form-group">--}}
                {{--<label for="">@lang('admin/super-admin.users.index.form.building.street')</label>--}}
                {{--<input type="text" name="building[street]" class="form-control">--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<div class="col-lg-3 col-sm-12">--}}
            {{--<div class="form-group">--}}
                {{--<label for="">@lang('admin/super-admin.users.index.form.building.number')</label>--}}
                {{--<input type="text" name="building[number]" class="form-control">--}}
            {{--</div>--}}
        {{--</div>--}}
    </div>

    <button type="submit" class="btn btn-default">@lang('admin/super-admin.users.index.form.submit')</button>
</form>
