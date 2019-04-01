@extends('cooperation.admin.layouts.app')

@section('content')

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.show.header', ['name' => $user->getFullName()])

        </div>
        <input type="hidden" name="user[id]" value="{{$user->id}}">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="role-select">@lang('woningdossier.cooperation.admin.users.show.role.label')</label>
                        <select @if(Auth::user()->hasRoleAndIsCurrentRole('coach')) disabled
                                @endif class="form-control" name="user[roles]" id="role-select" multiple="multiple">
                            @foreach($roles as $role)
                                <option @if($role->name != 'cooperation-admin') locked="locked" disabled="" @endif
                                         @if($user->hasRole($role)) selected="selected" @endif
                                        value="{{$role->id}}">
                                    {{$role->human_readable_name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        $(document).ready(function () {
            $('table').dataTable();

            var userId = $('input[name=user\\[id\\]]').val();

            $('#role-select').select2({
                templateSelection: function (tag, container) {
                    var option = $('#role-select option[value="' + tag.id + '"]');
                    if (option.attr('locked')) {
                        $(container).addClass('select2-locked-tag');
                        tag.locked = true
                    }

                    return tag.text;
                }
            }).on('select2:selecting', function (event) {
                var roleToSelect = $(event.params.args.data.element);

                if (confirm('@lang('woningdossier.cooperation.admin.users.show.give-role')')) {
                    $.ajax({
                        url: '{{route('cooperation.admin.roles.assign-role')}}',
                        method: 'POST',
                        data: {
                            role_id: roleToSelect.val(),
                            user_id: userId
                        }
                    }).done(function () {
                        // just reload the page
                        location.reload();
                    });
                } else {
                    event.preventDefault();
                    return false;
                }
            }).on('select2:unselecting', function (event) {
                var roleToUnselect = $(event.params.args.data.element);

                if (confirm('@lang('woningdossier.cooperation.admin.users.show.remove-role')')) {
                    $.ajax({
                        url: '{{route('cooperation.admin.roles.remove-role')}}',
                        method: 'POST',
                        data: {
                            role_id: roleToUnselect.val(),
                            user_id: userId
                        }
                    }).done(function () {
                        // just reload the page
                        location.reload();
                    });
                } else {
                    event.preventDefault();
                    return false;
                }
            });
        });
    </script>
@endpush
