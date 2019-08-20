@extends('cooperation.my-account.layouts.app')

@section('my_account_content')
    <form method="POST" action="{{ route('cooperation.my-account.hoom-settings.update', $account->id) }}" autocomplete="off">
        {{ method_field('PUT')  }}
        {{ csrf_field() }}


        <div class="panel panel-default">
            <div class="panel-heading">
                {{\App\Helpers\Translation::translate('my-account.hoom-settings.index.header')}}
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <p>{{\App\Helpers\Translation::translate('my-account.hoom-settings.index.text')}}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">


                        <div class="form-group{{ $errors->has('account.email') ? ' has-error' : '' }}">
                            <label for="email" class="control-label">@lang('my-account.hoom-settings.index.form.account.e-mail')</label>


                            <input id="email" type="email" class="form-control" name="account[email]"  value="{{ old('account.email', $account->email) }}" required autofocus>

                            @if ($errors->has('account.email'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('account.email') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <h3>@lang('my-account.hoom-settings.index.header-password')</h3>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group{{ $errors->has('account.current_password') ? ' has-error' : '' }}">
                            <label for="current_password" class="control-label">@lang('my-account.hoom-settings.index.form.account.current-password')</label>


                            <input id="current_password" type="password" class="form-control" name="account[current_password]">

                            @if ($errors->has('account.current_password'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('account.current_password') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group{{ $errors->has('account.password') ? ' has-error' : '' }}">
                            <label for="password"
                                   class="control-label">@lang('my-account.hoom-settings.index.form.account.new-password')</label>


                            <input id="password" type="password" class="form-control" name="account[password]">

                            @if ($errors->has('account.password'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('account.password') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group{{ $errors->has('account.password') || $errors->has('account.password_confirmation') ? ' has-error' : '' }}">
                            <label for="password-confirm" class="control-label">@lang('my-account.hoom-settings.index.form.account.new-password-confirmation')</label>


                            <input id="password-confirm" type="password" class="form-control" name="account[password_confirmation]">
                            @if ($errors->has('account.password') || $errors->has('account.password_confirmation'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('account.password') }}</strong>
                                    <strong>{{ $errors->first('account.password_confirmation') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                </div>
                <!-- password change section -->

                <hr>
                <div class="row">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary">
                                @lang('my-account.hoom-settings.index.form.submit')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection


@push('js')
    <script>
        var areYouSure = '@lang('my-account.hoom-settings.reset-file.are-you-sure')';
        $('#reset-account').click(function (event) {
            if (confirm(areYouSure)) {
                $(this).closest('form').submit();
            } else {
                event.preventDefault();
                return false;
            }
        });

        var userCooperationCount = '{{$account->users()->count()}}';

        var areYouSureToDestroy = '@lang('my-account.hoom-settings.destroy.are-you-sure.delete-from-cooperation')';

        if (userCooperationCount === 1) {
            areYouSureToDestroy = '@lang('my-account.hoom-settings.destroy.are-you-sure.complete-delete')';
        }

        $('#delete-account').click(function (event) {
            if (confirm(areYouSureToDestroy)) {
                $(this).closest('form').submit();
            } else {
                event.preventDefault();
                return false;
            }
        })
    </script>
@endpush