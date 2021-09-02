@extends('cooperation.frontend.layouts.tool')

{{--@section('step_title', __('wall-insulation.title.title'))--}}

@section('content')
    <div class="w-full flex flex-row flex-wrap">
        <div class="w-full space-y-10">
            <div class="flex flex-row flex-wrap w-full border border-solid border-blue-500 border-opacity-50 rounded-lg">
                <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 h-11 rounded-lg">
                    @lang('my-account.index.header')
                </div>
            </div>

            <form class="has-address-data" method="POST"
                  action="{{ route('cooperation.my-account.settings.update') }}"
                  autocomplete="off">
                @method('PUT')
                @csrf

                <input type="hidden" id="addressid" name="building[addressid]" value="{{$building->bag_addressid}}">

                <div class="flex flex-row flex-wrap w-full border border-solid border-blue-500 border-opacity-50 rounded-lg">
                    <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 h-11 rounded-t-lg border-b border-solid border-blue-500 border-opacity-50">
                        @lang('my-account.settings.index.header')
                    </div>

                    <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 py-8 rounded-b-lg">
                        <div class="w-full flex flex-row flex-wrap">
                            <div class="w-full">
                                @lang('my-account.settings.index.text')
                            </div>
                        </div>
                        <br>
                        <div class="w-full flex flex-row flex-wrap">
                            <div class="w-full sm:w-1/2 sm:pr-3">
                                @component('cooperation.frontend.layouts.components.form-group', [
                                    'withInputSource' => false,
                                    'label' => __('my-account.settings.index.form.user.first-name'),
                                    'inputName' => 'users.first_name',
                                    'id' => 'first_name',
                                ])
                                    <input id="first_name" type="text" class="form-input" name="user[first_name]"
                                           value="{{ old('user.first_name', $user->first_name) }}" required
                                           autofocus>
                                @endcomponent
                            </div>
                            <div class="w-full sm:w-1/2 sm:pl-3">
                                @component('cooperation.frontend.layouts.components.form-group', [
                                    'withInputSource' => false,
                                    'label' => __('my-account.settings.index.form.user.last-name'),
                                    'inputName' => 'users.last_name',
                                    'id' => 'last_name',
                                ])
                                    <input id="last_name" type="text" class="form-input" name="user[last_name]"
                                           value="{{ old('last_name', $user->last_name) }}" required autofocus>
                                @endcomponent
                            </div>
                        </div>

                        <div class="w-full flex flex-row flex-wrap">
                            <div class="w-full sm:w-1/2 sm:pr-3">
                                @component('cooperation.frontend.layouts.components.form-group', [
                                    'withInputSource' => false,
                                    'label' => __('my-account.settings.index.form.user.phone_number'),
                                    'inputName' => 'users.phone_number',
                                    'id' => 'phone_number',
                                ])
                                    <input id="phone_number" type="text" class="form-input"
                                           name="user[phone_number]"
                                           value="{{ old('phone_number', $user->phone_number) }}" autofocus>
                                @endcomponent
                            </div>
                        </div>

                        <div class="w-full flex flex-row flex-wrap mt-5">
                            <div class="w-full">
                                <h4 class="heading-4">
                                    @lang('my-account.settings.index.header-building')
                                </h4>
                            </div>

                            <div class="w-full sm:w-1/3 sm:pr-3">
                                @component('cooperation.frontend.layouts.components.form-group', [
                                    'withInputSource' => false,
                                    'label' => __('my-account.settings.index.form.building.postal-code'),
                                    'inputName' => 'building.postal_code',
                                    'id' => 'postal_code',
                                ])
                                    <input type="text" class="form-input" name="building[postal_code]"
                                           id="postal_code"
                                           value="{{ old('building.postal_code', $building->postal_code) }}"
                                           required autofocus>
                                @endcomponent
                            </div>

                            <div class="w-full sm:w-1/3 sm:px-3">
                                @component('cooperation.frontend.layouts.components.form-group', [
                                    'withInputSource' => false,
                                    'label' => __('my-account.settings.index.form.building.number'),
                                    'inputName' => 'building.number',
                                    'id' => 'number',
                                ])
                                    <input type="text" class="form-input" name="building[house_number]"
                                           id="number"
                                           value="{{ old('building.house_number', $building->number) }}" required
                                           autofocus>
                                @endcomponent
                            </div>

                            <div class="w-full sm:w-1/3 sm:pl-3">
                                @component('cooperation.frontend.layouts.components.form-group', [
                                    'withInputSource' => false,
                                    'label' => __('my-account.settings.index.form.building.extension'),
                                    'inputName' => 'building.extension',
                                    'id' => 'extension',
                                ])
                                    <input type="text" class="form-input" name="building[extension]"
                                           id="house_number_extension"
                                           value="{{ old('building.extension', $building->extension) }}" autofocus>
                                @endcomponent
                            </div>
                        </div>

                        <div class="w-full flex flex-row flex-wrap">
                            <div class="w-full sm:w-1/2 sm:pr-3">
                                @component('cooperation.frontend.layouts.components.form-group', [
                                    'withInputSource' => false,
                                    'label' => __('my-account.settings.index.form.building.street'),
                                    'inputName' => 'building.street',
                                    'id' => 'street',
                                ])
                                    <input type="text" class="form-input" name="building[street]" id="street"
                                           value="{{ old('building.street', $building->street) }}" required
                                           autofocus>
                                @endcomponent
                            </div>

                            <div class="w-full sm:w-1/2 sm:pl-3">
                                @component('cooperation.frontend.layouts.components.form-group', [
                                    'withInputSource' => false,
                                    'label' => __('my-account.settings.index.form.building.city'),
                                    'inputName' => 'building.city',
                                    'id' => 'city',
                                ])
                                    <input type="text" class="form-input" name="building[city]" id="city"
                                           value="{{ old('building.city', $building->city) }}" required autofocus>
                                @endcomponent
                            </div>
                        </div>

                        <hr class="w-full">

                        <div class="w-full flex flex-row flex-wrap">
                            <div class="form-group">
                                <div class="w-full">
                                    <button type="submit" class="btn btn-green">
                                        @lang('my-account.settings.index.form.submit')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <form method="POST" action="{{ route('cooperation.my-account.hoom-settings.update', $account->id) }}"
                  autocomplete="off">
                @method('PUT')
                @csrf

                <div class="flex flex-row flex-wrap w-full border border-solid border-blue-500 border-opacity-50 rounded-lg">
                    <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 h-11 rounded-t-lg border-b border-solid border-blue-500 border-opacity-50">
                        @lang('my-account.hoom-settings.index.header')
                    </div>

                    <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 py-8 rounded-b-lg">
                        <div class="w-full flex flex-row flex-wrap">
                            <div class="w-full">
                                <p>@lang('my-account.hoom-settings.index.text')</p>
                            </div>
                        </div>
                        <div class="w-full flex flex-row flex-wrap">
                            <div class="w-full sm:w-1/2 sm:pr-3">
                                @component('cooperation.frontend.layouts.components.form-group', [
                                    'withInputSource' => false,
                                    'label' => __('my-account.hoom-settings.index.form.account.e-mail'),
                                    'inputName' => 'account.email',
                                    'id' => 'email',
                                ])
                                    <input id="email" type="email" class="form-input" name="account[email]"
                                           value="{{ old('account.email', $account->email) }}" required autofocus>
                                @endcomponent
                            </div>

                            <div class="w-full mt-5">
                                <h4 class="heading-4">
                                    @lang('my-account.hoom-settings.index.header-password')
                                </h4>
                            </div>
                            <div class="w-full">
                                @component('cooperation.frontend.layouts.components.form-group', [
                                    'withInputSource' => false,
                                    'label' => __('my-account.hoom-settings.index.form.account.current-password'),
                                    'inputName' => 'account.current_password',
                                    'id' => 'current_password',
                                ])
                                    <input id="current_password" type="password" class="form-input"
                                           name="account[current_password]">
                                @endcomponent
                            </div>
                            <div class="w-full sm:w-1/2 sm:pr-3">
                                @component('cooperation.frontend.layouts.components.form-group', [
                                    'withInputSource' => false,
                                    'label' => __('my-account.hoom-settings.index.form.account.new-password'),
                                    'inputName' => 'account.password',
                                    'id' => 'password',
                                ])
                                    <input id="password" type="password" class="form-input"
                                           name="account[password]">
                                @endcomponent
                            </div>

                            <div class="w-full sm:w-1/2 sm:pl-3">
                                @component('cooperation.frontend.layouts.components.form-group', [
                                    'withInputSource' => false,
                                    'label' => __('my-account.hoom-settings.index.form.account.new-password-confirmation'),
                                    'inputName' => 'account.password_confirmation',
                                    'id' => 'password-confirm',
                                ])
                                    <input id="password-confirm" type="password" class="form-input"
                                           name="account[password_confirmation]">
                                @endcomponent
                            </div>

                        </div>
                        <!-- password change section -->

                        <hr class="w-full">

                        <div class="w-full flex flex-row flex-wrap">
                            <div class="form-group">
                                <div class="w-full">
                                    <button type="submit" class="btn btn-green">
                                        @lang('my-account.hoom-settings.index.form.submit')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="flex flex-row flex-wrap w-full border border-solid border-blue-500 border-opacity-50 rounded-lg">
                <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 h-11 rounded-t-lg border-b border-solid border-blue-500 border-opacity-50">
                    @lang('my-account.settings.reset-file.header')
                </div>

                <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 py-8 rounded-b-lg" x-data="modal()">
                    <p class="w-full">
                        @lang('my-account.settings.reset-file.description')
                    </p>

                    @component('cooperation.frontend.layouts.components.form-group', [
                        'withInputSource' => false,
                        'label' => __('my-account.settings.reset-file.label'),
                        'id' => 'reset-file',
                    ])
                        <a class="btn btn-red" x-on:click="toggle()">
                            @lang('my-account.settings.reset-file.submit')
                        </a>
                    @endcomponent

                    @component('cooperation.frontend.layouts.components.modal', [
                        'header' => __('my-account.settings.reset-file.modal.title'),
                    ])
                        <p>
                            @lang('my-account.settings.reset-file.modal.text')
                        </p>

                        <div class="w-full mt-3 space-y-3">
                            <form method="POST"
                                  action="{{ route('cooperation.my-account.settings.reset-file', compact('cooperation')) }}">
                                @csrf
                                <input type="hidden" name="input_sources[id][]"
                                       value="{{\App\Models\InputSource::findByShort('resident')->id}}">
                                <button type="button"
                                        class="reset-account btn btn-red">
                                    @lang('my-account.settings.reset-file.modal.reset-resident')
                                </button>
                            </form>
                            <form method="POST"
                                  action="{{ route('cooperation.my-account.settings.reset-file', compact('cooperation')) }}">
                                @csrf
                                <input type="hidden" name="input_sources[id][]"
                                       value="{{\App\Models\InputSource::findByShort('resident')->id}}">
                                <input type="hidden" name="input_sources[id][]"
                                       value="{{\App\Models\InputSource::findByShort('coach')->id}}">
                                <button type="button"
                                        class="reset-account btn btn-red">
                                    @lang('my-account.settings.reset-file.modal.reset-both')
                                </button>
                            </form>
                        </div>
                    @endcomponent
                </div>
            </div>

            @can('delete-own-account')
                <div class="flex flex-row flex-wrap w-full border border-solid border-blue-500 border-opacity-50 rounded-lg">
                    <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 h-11 rounded-t-lg border-b border-solid border-blue-500 border-opacity-50">@lang('my-account.settings.destroy.header')</div>

                    <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 py-8 rounded-b-lg">
                        <form method="POST"
                              action="{{ route('cooperation.my-account.settings.destroy', ['cooperation' => $cooperation]) }}">
                            @method('DELETE')
                            @csrf

                            <div class="w-full flex flex-row flex-wrap">
                                <div class="w-full">
                                    @component('cooperation.frontend.layouts.components.form-group', [
                                        'withInputSource' => false,
                                        'label' => __('my-account.settings.destroy.label'),
                                        'id' => 'delete-account',
                                        'class' => '-mt-4',
                                    ])
                                        <button type="submit" id="delete-account" class="btn btn-red">
                                            @lang('my-account.settings.destroy.submit')
                                        </button>
                                    @endcomponent
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endcan

            {{-- Notification settings --}}
            <div class="flex flex-row flex-wrap w-full border border-solid border-blue-500 border-opacity-50 rounded-lg">
                <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 h-11 rounded-t-lg border-b border-solid border-blue-500 border-opacity-50">
                    @lang('my-account.notification-settings.index.header')
                </div>

                <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 py-8 rounded-b-lg">
                    <div class="w-full flex flex-row flex-wrap">
                        <div class="w-full">
                            <table id="table" class="table fancy-table">
                                <thead>
                                <tr>
                                    <th>@lang('my-account.notification-settings.index.table.columns.name')</th>
                                    <th>@lang('my-account.notification-settings.index.table.columns.interval')</th>
                                    <th>@lang('my-account.notification-settings.index.table.columns.last-notified-at')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($notificationSettings as $i => $notificationSetting)
                                    <tr>
                                        <td>{{ $notificationSetting->type->name }}</td>
                                        <td>

                                            <form action="{{route('cooperation.my-account.notification-settings.update', $notificationSetting->id)}}"
                                                  method="post" id="change-interval-form">
                                                @method('PUT')
                                                @csrf
                                                @component('cooperation.frontend.layouts.components.alpine-select')
                                                    <select name="notification_setting[{{$notificationSetting->id}}][interval_id]"
                                                            class="form-input change-interval">
                                                        @foreach($notificationIntervals as $notificationInterval)
                                                            <option @if(old('notification_setting.interval_id', $notificationSetting->interval_id) == $notificationInterval->id) selected="selected"
                                                                    @endif value="{{$notificationInterval->id}}">
                                                                {{$notificationInterval->name}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @endcomponent
                                            </form>
                                        </td>
                                        <td>{{ is_null($notificationSetting->last_notified_at) ? __('my-account.notification-settings.index.table.never-sent') : $notificationSetting->last_notified_at->format('Y-m-d') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{--Access--}}
            <div class="flex flex-row flex-wrap w-full border border-solid border-blue-500 border-opacity-50 rounded-lg">
                <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 h-11 rounded-t-lg border-b border-solid border-blue-500 border-opacity-50">
                    @lang('my-account.access.index.header')
                </div>

                <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 py-8 rounded-b-lg">
                    <div class="w-full flex flex-row flex-wrap">
                        <div class="w-full">
                            <form id="allow-access-form"
                                  action="{{route('cooperation.my-account.access.allow-access')}}"
                                  method="post">
                                @csrf
                                @component('cooperation.frontend.layouts.components.form-group', [
                                    'withInputSource' => false,
                                    'inputName' => 'allow_access',
                                    'class' => '-mt-4',
                                ])
                                    <div class="checkbox-wrapper">
                                        <input id="allow-access" name="allow_access" type="checkbox" value="1"
                                               @if(old('allow_access') && old('allow_access') == '1' || $user->allowedAccess()) checked="checked" @endif>
                                        <label for="allow-access">
                                            <span class="checkmark"></span>
                                            <span>
                                                @lang('my-account.access.index.form.allow_access', ['cooperation' => \App\Helpers\HoomdossierSession::getCooperation(true)->name])
                                            </span>
                                        </label>
                                    </div>
                                    <p class="text-left">
                                        @lang('my-account.access.index.text-allow-access')
                                    </p>
                                @endcomponent
                            </form>
                        </div>
                    </div>
                    <div class="w-full flex flex-row flex-wrap mt-5">
                        <div class="w-full">
                            <table id="table" class="table fancy-table">
                                <thead>
                                <tr>
                                    <th>@lang('my-account.access.index.table.columns.coach')</th>
                                    <th>@lang('my-account.access.index.table.columns.actions')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($buildingPermissions as $i => $buildingPermission)
                                    <form id="revoke-access-{{$buildingPermission->id}}"
                                          action="{{route('cooperation.messages.participants.revoke-access')}}"
                                          method="post">
                                        @csrf
                                        <input type="hidden" name="user_id"
                                               value="{{$buildingPermission->user_id}}">
                                        <input type="hidden" name="building_owner_id"
                                               value="{{$buildingPermission->building_id}}">
                                    </form>
                                    <tr>
                                        <td>{{ $buildingPermission->user->getFullName() }}</td>
                                        <td>
                                            <a data-form-id="revoke-access-{{$buildingPermission->id}}"
                                               class="revoke-access btn btn-red">
                                                <i class="glyphicon glyphicon-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        $(document).ready(function () {
            $('.change-interval').change(function () {
                $(this).parents('#change-interval-form').first().submit();
            });

            $('.revoke-access').click(function () {
                if (confirm('Weet u zeker dat u deze gebruiker de toegang wilt ontzetten ?')) {
                    var formId = $(this).data('form-id');
                    $('form#' + formId).submit();
                }
            });

            $('#allow_access').change(function () {
                // if access gets turned of, we want to show them a alert
                // else we dont!
                if ($(this).prop('checked') === false) {
                    if (confirm('Weet u zeker dat u de toegang wilt ontzeggen voor elk gekoppelde coach ?')) {
                        $('#allow-access-form').submit();
                    } else {
                        // otherwise this may seems weird, so on cancel. we check the box again.
                        $(this).prop('checked', true);
                    }
                } else {
                    $('#allow-access-form').submit();
                }
            });


            var areYouSure = '@lang('my-account.settings.reset-file.are-you-sure')';
            $('.reset-account').click(function (event) {
                if (confirm(areYouSure)) {
                    $(this).closest('form').submit();
                }
            });

            var userCooperationCount = '{{$account->users()->count()}}';

            var areYouSureToDestroy = '@lang('my-account.settings.destroy.are-you-sure.delete-from-cooperation')';

            if (userCooperationCount === 1) {
                areYouSureToDestroy = '@lang('my-account.settings.destroy.are-you-sure.complete-delete')';
            }

            $('#delete-account').click(function (event) {
                if (confirm(areYouSureToDestroy)) {
                    $(this).closest('form').submit();
                } else {
                    event.preventDefault();
                    return false;
                }
            })
        })
    </script>
@endpush
