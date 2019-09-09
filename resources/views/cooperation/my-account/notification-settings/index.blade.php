@extends('cooperation.my-account.layouts.app')

@section('my_account_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('my-account.notification-settings.index.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-responsive table-bordered compact nowrap">
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

                                <form action="{{route('cooperation.my-account.notification-settings.update', $notificationSetting->id)}}" method="post">
                                    {{csrf_field()}}
                                    {{method_field('put')}}
                                    <select name="notification_setting[{{$notificationSetting->id}}][interval_id]" class="form-control change-interval">
                                        @foreach($notificationIntervals as $notificationInterval)
                                            <option @if(old('notification_setting.interval_id', $notificationSetting->interval_id) == $notificationInterval->id) selected="selected" @endif value="{{$notificationInterval->id}}">{{$notificationInterval->name}}</option>
                                        @endforeach
                                    </select>
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
@endsection


@push('js')
    <script>
        $(document).ready(function () {
            $('.change-interval').change(function () {
                $(this).parent().submit();
            });
        })
    </script>
@endpush