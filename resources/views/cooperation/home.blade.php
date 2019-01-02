@extends('cooperation.layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#start" data-toggle="start">@lang('woningdossier.cooperation.home.disclaimer.tabs.start')</a>
                </li>
                <li>
                    <a href="https://form.jotformeu.com/81345355694363" target="_blank" data-toggle="disclaimer">@lang('woningdossier.cooperation.home.disclaimer.tabs.bugreport')</a>
                </li>
                <li>
                    <a href="{{route('cooperation.my-account.messages.index', ['cooperation' => $cooperation])}}" target="_blank">
                        @lang('woningdossier.cooperation.home.disclaimer.tabs.messages')
                        <span class="badge">{{$myUnreadMessages->count()}}</span>
                    </a>
                </li>
                <li>
                    <a href="#privacy" data-toggle="privacy">
                        @lang('woningdossier.cooperation.home.disclaimer.tabs.privacy')
                    </a>
                </li>
                <li>
                    <a href="#disclaimer" data-toggle="disclaimer">
                        @lang('woningdossier.cooperation.home.disclaimer.tabs.disclaimer')
                    </a>
                </li>
                <li>
                    <a href="{{route('cooperation.my-account.settings.index')}}" target="_blank">
                        @lang('woningdossier.cooperation.home.disclaimer.tabs.settings')
                    </a>
                </li>
            </ul>
            <div class="tab-content">

                <div class="panel tab-pane active tab-pane panel-default" id="start">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                @lang('woningdossier.cooperation.home.disclaimer.description')
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <a href="{{route('cooperation.conversation-requests.index', ['cooperation' => $cooperation, 'action' => \App\Models\PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION])}}" class="btn btn-primary">@lang('woningdossier.cooperation.help.help.help-with-filling-tool')</a>
                                <a href="{{route('cooperation.tool.general-data.index', ['cooperation' => $cooperation])}}" class="btn btn-primary">@lang('woningdossier.cooperation.help.help.no-help-with-filling-tool')</a>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-sm-12">
                                <a class="btn btn-primary" href="{{ route('cooperation.tool.index', ['cooperation' => $cooperation]) }}">@lang('woningdossier.cooperation.tool.title') <i class="glyphicon glyphicon-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel tab-pane active tab-pane panel-default" id="disclaimer">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                @lang('woningdossier.cooperation.home.disclaimer.description')
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-sm-12">
                                <a class="btn btn-primary" href="{{ route('cooperation.tool.index', ['cooperation' => $cooperation]) }}">@lang('woningdossier.cooperation.tool.title') <i class="glyphicon glyphicon-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel tab-pane active tab-pane panel-default" id="privacy">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                @lang('woningdossier.cooperation.home.disclaimer.description')
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-sm-12">
                                <a class="btn btn-primary" href="{{ route('cooperation.tool.index', ['cooperation' => $cooperation]) }}">@lang('woningdossier.cooperation.tool.title') <i class="glyphicon glyphicon-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script>
        $('ul.nav.nav-pills li a').click(function() {
            $(this).parent().addClass('active').siblings().removeClass('active');
        });
    </script>
@endpush
