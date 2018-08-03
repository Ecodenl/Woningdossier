@extends('cooperation.layouts.app')


@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('woningdossier.cooperation.conversation-request.coach.index.header')</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('cooperation.conversation-request.coach.store', ['cooperation' => $cooperation]) }}">
                            {{ csrf_field() }}

                            <h2>@lang('woningdossier.cooperation.conversation-request.coach.index.header')</h2>
                            <p>@lang('woningdossier.cooperation.conversation-request.coach.index.text')</p>


                            <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
                                <div class="col-sm-12">
                                    <textarea name="message" class="form-control">@if(isset($privateMessage)){{$privateMessage->message}} @else @lang('woningdossier.cooperation.conversation-request.coach.index.form.message') @endif </textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12 ">
                                    <button type="submit" class="btn btn-primary">
                                        @if(isset($privateMessage))
                                            @lang('woningdossier.cooperation.conversation-request.coach.index.form.update')
                                        @else
                                            @lang('woningdossier.cooperation.conversation-request.coach.index.form.submit')
                                        @endif
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
