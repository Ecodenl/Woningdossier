@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.coach.connect-to-resident.create.header', ['firstName' => $receiver->first_name, 'lastName' => $receiver->last_name])
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{route('cooperation.admin.coach.connect-to-resident.store')}}" method="post"  >
                        {{csrf_field()}}
                        <input type="hidden" name="receiver_id" value="{{$receiver->id}}">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group" {{ $errors->has('title') ? ' has-error' : '' }}>
                                    <label for="title">@lang('woningdossier.cooperation.admin.coach.connect-to-resident.create.form.title.label')</label>
                                    <input name="title" id="" class="form-control" placeholder="{{old('title', __('woningdossier.cooperation.admin.coach.connect-to-resident.create.form.title.placeholder'))}}">
                                    @if ($errors->has('title'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('title') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group" {{ $errors->has('message') ? ' has-error' : '' }}>
                                    <label for="message">@lang('woningdossier.cooperation.admin.coach.connect-to-resident.create.form.message.label')</label>
                                    <textarea name="message" id="" class="form-control">{{old('message', __('woningdossier.cooperation.admin.coach.connect-to-resident.create.form.message.placeholder'))}}</textarea>
                                    @if ($errors->has('message'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('message') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <button class="btn btn-primary btn-block" type="submit">@lang('woningdossier.cooperation.admin.coach.connect-to-resident.create.form.submit') <span class="glyphicon glyphicon-plus"></span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection



@push('js')

    <script>

        $(document).ready(function () {
            $('form').disableAutoFill();
        });

    </script>
@endpush

