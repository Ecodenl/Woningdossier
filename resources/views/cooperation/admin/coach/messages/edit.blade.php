@extends('cooperation.admin.coach.layouts.app')

@section('coach_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            {{ $privateMessages->first()->title }}
            @can('respond', $mainMessageId)
                @if(!\App\Models\User::find($privateMessages->first()->from_user_id)->hasRole('coordinator'))
                <a id="revoke-access">
                    <span class="pull-right label label-success">Ik wil geen contact meer met deze bewoner</span>
                </a>
                @endif
            @endcan
        </div>
        <div class="panel-body panel-chat-body" id="chat">
            <form id="revoke-access-form" action="{{ route('cooperation.admin.coach.messages.revoke-access') }}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="main_message_id" value="{{$mainMessageId}}">
            </form>
            @component('cooperation.layouts.chat.messages')
                @forelse($privateMessages->sortBy('created_at') as $privateMessage)

                    <?php $time = \Carbon\Carbon::parse($privateMessage->created_at); ?>

                    <li class="@if($privateMessage->isMyMessage()) right @else left @endif clearfix">
                        <div class="chat-body clearfix">
                            <div class="header">
                                @if($privateMessage->isMyMessage())

                                    <small class="text-muted"><span class="glyphicon glyphicon-time"></span>{{ $time->diffForHumans() }}</small>
                                    <strong class="pull-right primary-font">{{ $privateMessage->getSender($privateMessage->id)->first_name }}</strong>

                                @else

                                    <strong class="primary-font">{{ $privateMessage->getSender($privateMessage->id)->first_name }}</strong>
                                    <small class="pull-right text-muted"><span class="glyphicon glyphicon-time"></span>{{ $time->diffForHumans() }}</small>

                                @endif
                            </div>
                            <p>
                                {{ $privateMessage->message }}
                            </p>
                        </div>
                    </li>
                @empty

                @endforelse
            @endcomponent
        </div>

        <div class="panel-footer">
            @component('cooperation.layouts.chat.input', ['privateMessages' => $privateMessages, 'mainMessageId' => $mainMessageId, 'url' => route('cooperation.admin.coach.messages.store')])
                <button type="submit" class="btn btn-primary btn-md" id="btn-chat">
                    @lang('woningdossier.cooperation.admin.coach.messages.edit.send')
                </button>
            @endcomponent
        </div>
    </div>


@endsection

@push('js')
    <script>
        $('document').ready(function () {
            $('#revoke-access').on('click', function () {
                if (confirm('Weet u zeker dat u geen contact wilt met deze coach? Er wordt hierna een nieuwe coach voor u gezocht. Dit kan enige tijd duren.')) {
                    $('#revoke-access-form').submit();
                }
            });
        })
    </script>
@endpush