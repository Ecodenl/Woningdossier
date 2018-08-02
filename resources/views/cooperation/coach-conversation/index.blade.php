@extends('cooperation.layouts.app')


@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('woningdossier.cooperation.coach-conversation-request.index.header')</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('cooperation.coach-conversation-request.store', ['cooperation' => $cooperation]) }}">
                            {{ csrf_field() }}

                            <h2>@lang('woningdossier.cooperation.coach-conversation-request.index.header')</h2>
                            <p>@lang('woningdossier.cooperation.coach-conversation-request.index.text')</p>


                            <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
                                <div class="col-sm-12">
                                    <textarea name="message" class="form-control">@if(isset($privateMessage)){{$privateMessage->message}} @else @lang('woningdossier.cooperation.coach-conversation-request.index.form.message') @endif </textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12 ">
                                    <button type="submit" class="btn btn-primary">
                                        @if(isset($privateMessage))
                                            @lang('woningdossier.cooperation.coach-conversation-request.index.form.update')
                                        @else
                                            @lang('woningdossier.cooperation.coach-conversation-request.index.form.submit')
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
