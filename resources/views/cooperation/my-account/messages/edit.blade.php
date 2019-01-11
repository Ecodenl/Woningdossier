@extends('cooperation.my-account.layouts.app')

@section('my_account_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            {{$privateMessages->first()->title}}
            @can('respond', $mainMessageId)
                <a id="revoke-access">
                    <span class="pull-right label label-success">Ik wil geen contact meer met deze coach</span>
                </a>
            @endcan
        </div>
        <div class="panel-body panel-chat-body" id="chat">
            <form id="revoke-access-form" action="{{route('cooperation.my-account.messages.revoke-access')}}" method="post">
                {{csrf_field()}}
                <input type="hidden" name="main_message_id" value="{{$mainMessageId}}">
            </form>
            @component('cooperation.layouts.chat.messages')
                @forelse($privateMessages->sortBy('created_at') as $privateMessage)

                    <?php $time = \Carbon\Carbon::parse($privateMessage->created_at); ?>

                    <li class="@if($privateMessage->isMyMessage()) right @else left @endif clearfix">
                        <div class="chat-body clearfix">
                            <div class="header">
                                @if($privateMessage->isMyMessage())

                                    <small class="text-muted"><span class="glyphicon glyphicon-time"></span>{{$time->diffForHumans()}}</small>
                                    <strong class="pull-right primary-font">{{$privateMessage->getSender()}}</strong>

                                @else

                                    <strong class="primary-font">{{$privateMessage->getSender()}}</strong>
                                    <small class="pull-right text-muted"><span class="glyphicon glyphicon-time"></span>{{$time->diffForHumans()}}</small>

                                @endif
                            </div>
                            <p>
                                {{$privateMessage->message}}
                            </p>
                        </div>
                    </li>
                @empty

                @endforelse
            @endcomponent
        </div>
        <div class="panel-footer">
            @component('cooperation.layouts.chat.input', ['privateMessages' => $privateMessages, 'mainMessageId' => $mainMessageId, 'url' => route('cooperation.my-account.messages.store')])
                <button type="submit" class="btn btn-primary btn-md" id="btn-chat">
                    @lang('woningdossier.cooperation.my-account.messages.edit.chat.button')
                </button>
            @endcomponent
        </div>
    </div>


@endsection

@push('js')
    <script>
        $('document').ready(function () {
            $('#revoke-access').on('click', function (event) {
                // Bij de "weet je het zeker" voor de gebruiker aangeven dat de toegang tot de woning word ingetrokken en de aanvraag bij de cooperatie wordt teruggelegd
                if (confirm('Weet u zeker dat u geen contact wilt met deze coach, de toegang tot de woning word voor de coach ingetrokken. Ook word de cooperatie teruggelegd bij de cooperatie, het kan enige tijd duren tot er een nieuwe coach word gekoppeld.')) {
                    $('#revoke-access-form').submit();
                } else {
                    event.preventDefault();
                    return false;
                }
            });
        })
    </script>
@endpush