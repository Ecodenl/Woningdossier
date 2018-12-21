@extends('cooperation.my-account.layouts.app')

@section('my_account_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            {{$conversationRequest->title}}
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <form class="form-horizontal" method="POST" action="{{ route('cooperation.my-account.messages.requests.update', ['requestMessageId' => $conversationRequest->id] ) }}">
                        {{ csrf_field() }}
                        @if(\App\Models\PrivateMessage::isConversationRequestConnectedToCoach($conversationRequest))
                            <p>@lang('woningdossier.cooperation.my-account.messages.requests.edit.is-connected-to-coach')</p>
                        @endif


                        <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
                            <div class="col-sm-12">
                                <textarea @if(\App\Models\PrivateMessage::isConversationRequestConnectedToCoach($conversationRequest)) disabled @endif name="message" class="form-control">@if(isset($conversationRequest)){{$conversationRequest->message}}@else@lang('woningdossier.cooperation.conversation-requests.more-information.index.form.message')@endif</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12 ">
                                <button type="submit" @if(\App\Models\PrivateMessage::isConversationRequestConnectedToCoach($conversationRequest)) disabled @endif class="btn btn-primary">
                                    @lang('default.buttons.update')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
