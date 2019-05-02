@extends('cooperation.my-account.layouts.app')

@section('my_account_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.my-account.notification-settings.index.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{route('cooperation.my-account.notification-settings.update', ['id' => $notificationSetting->id])}}" method="post">
                        {{method_field('PUT')}}
                        {{csrf_field()}}

                        <div class="form-group">
                            <label for="">@lang('woningdossier.cooperation.my-account.notification-settings.show.form.interval', ['type' => $notificationSetting->type->name])</label>
                            <select name="notification_setting[interval_id]" class="form-control">
                                @foreach($notificationIntervals as $notificationInterval)
                                    <option @if(old('notification_setting.interval_id', $notificationSetting->interval_id) == $notificationInterval->id) selected="selected" @endif value="{{$notificationInterval->id}}">{{$notificationInterval->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">@lang('woningdossier.cooperation.my-account.notification-settings.show.form.submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection