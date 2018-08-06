@extends('cooperation.my-account.messages.layout')

@section('messages_header', __('woningdossier.cooperation.conversation-request.more-information.index.header' ))
@section('messages_content')

    <form class="form-horizontal" method="POST" action="{{ route('cooperation.my-account.messages.requests.update', ['requestMessageId' => $conversationRequest->id] ) }}">
        {{ csrf_field() }}

        <h2>@lang('woningdossier.cooperation.conversation-request.more-information.index.header' )</h2>
        <p>@lang('woningdossier.cooperation.conversation-request.more-information.index.text')</p>


        <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
            <div class="col-sm-12">
                <textarea name="message" class="form-control">@if(isset($conversationRequest)){{$conversationRequest->message}} @else @lang('woningdossier.cooperation.conversation-request.more-information.index.form.message') @endif </textarea>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-12 ">
                <button type="submit" class="btn btn-primary">
                    @lang('woningdossier.cooperation.conversation-request.more-information.index.form.update')
                </button>
            </div>
        </div>
    </form>

@endsection