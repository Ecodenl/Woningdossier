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

                        @if(\App\Models\PrivateMessage::isConversationRequestConnectedToCoach($conversationRequest))
                            <input type="hidden" name="message" value="{{$conversationRequest->message}}">
                        @endif


                        <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
                            <div class="col-sm-12">
                                <label for="">@lang('woningdossier.cooperation.conversation-requests.index.form.message')</label>

                                <textarea @if(\App\Models\PrivateMessage::isConversationRequestConnectedToCoach($conversationRequest)) disabled @endif name="message" class="form-control">@if(isset($conversationRequest)){{$conversationRequest->message}}
                                    @else@lang('woningdossier.cooperation.conversation-requests.more-information.index.form.message')
                                    @endif
                                </textarea>

                                @if ($errors->has('message'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('message') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('allow_access') ? ' has-error' : '' }}">
                            <div class="col-sm-12">
                                <label for="allow_access">
                                    <input id="allow_access" name="allow_access" type="checkbox" @if(old('allow_access') && old('allow_access') == 'on') @elseif(isset($conversationRequest) && $conversationRequest->allow_access)checked="checked"@endif>
                                    @lang('woningdossier.cooperation.conversation-requests.index.form.allow_access', ['cooperation' => \App\Models\Cooperation::find(session('cooperation'))->name])
                                </label>
                                @if ($errors->has('allow_access'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('allow_access') }}</strong>
                                    </span>
                                @endif
                                <p>@lang('woningdossier.cooperation.conversation-requests.index.text')</p>
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-12 ">
                                <button type="submit" class="btn btn-primary">
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
