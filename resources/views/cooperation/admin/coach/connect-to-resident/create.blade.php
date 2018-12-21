@extends('cooperation.admin.coach.layouts.app')

@section('coach_content')
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
                                <div class="form-group" {{ $errors->has('message') ? ' has-error' : '' }}>
                                    <label for="message">@lang('woningdossier.cooperation.admin.coach.connect-to-resident.create.form.message.label')</label>
                                    <textarea name="message" id="" class="form-control">@lang('woningdossier.cooperation.admin.coach.connect-to-resident.create.form.message.placeholder')</textarea>
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
                                <div class="form-group" {{ $errors->has('conversation-request-type') ? ' has-error' : '' }}>
                                    <label for="conversation-request-type">@lang('woningdossier.cooperation.admin.coach.connect-to-resident.create.form.request-type.label')</label>
                                    <select name="conversation-request-type" class="conversation-request-type form-control" id="conversation-request-type" >
                                        @foreach(__('woningdossier.cooperation.conversation-requests.edit.form.options') as $value => $label)
                                            <option value="{{$value}}">{{$label}}</option>
                                        @endforeach
                                    </select>


                                    @if ($errors->has('conversation-request-type'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('conversation-request-type') }}</strong>
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



@push('css')
    <link rel="stylesheet" href="{{asset('css/select2/select2.min.css')}}">
@push('js')
    <script src="{{asset('js/select2.js')}}"></script>

    <script>

        $(document).ready(function () {

            $(".conversation-request-type").select2({
                placeholder: "@lang('woningdossier.cooperation.admin.connect-to-resident.create.form.request-type.placeholder')",
            });
        });

        $('form').disableAutoFill();
    </script>
@endpush

