@extends('cooperation.layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#start" data-toggle="tab">@lang('woningdossier.cooperation.home.tabs.start')</a>
                </li>
                <li>
                    <a href="{{route('cooperation.my-account.messages.index', ['cooperation' => $cooperation])}}">
                        @lang('woningdossier.cooperation.home.tabs.messages')
                        <span class="badge">{{\App\Models\PrivateMessageView::getTotalUnreadMessagesForCurrentRole()}}</span>
                    </a>
                </li>
                <li>
                    <a href="#privacy" data-toggle="tab">
                        @lang('woningdossier.cooperation.home.tabs.privacy')
                    </a>
                </li>
                <li>
                    <a href="#disclaimer" data-toggle="tab">
                        @lang('woningdossier.cooperation.home.tabs.disclaimer')
                    </a>
                </li>
                <li>
                    <a href="{{route('cooperation.my-account.index')}}">
                        @lang('woningdossier.cooperation.home.tabs.settings')
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="panel tab-pane active tab-pane panel-default" id="start">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-12">
                                        {!! \App\Helpers\Translation::translate('home.start.dear-user.title') !!}
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="text-center col-sm-12">
                                        <a href="{{route('cooperation.tool.building-detail.index', ['cooperation' => $cooperation])}}" class="btn btn-primary">
                                            {!!  \App\Helpers\Translation::translate('home.start.get-started.title') !!}
                                        </a>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-sm-12">
                                        {!! \App\Helpers\Translation::translate('home.start.your-cooperation.title', ['cooperation' => $cooperation->name]) !!}
                                    </div>
                                    <div class="text-center col-sm-12">
                                        <a href="{{route('cooperation.conversation-requests.index', ['cooperation' => $cooperation])}}"  class="btn btn-primary">
                                            {!!  \App\Helpers\Translation::translate('home.start.contact-cooperation.title', ['cooperation' => $cooperation->name]) !!}
                                        </a>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-sm-12">
                                        {!! \App\Helpers\Translation::translate('home.start.feedback.title') !!}
                                    </div>
                                    <div class="text-center col-sm-12">
                                        <a href="https://form.jotformeu.com/81345355694363" target="_blank" class="btn btn-primary">
                                            {!! \App\Helpers\Translation::translate('home.start.feedback-button.title') !!}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel tab-pane tab-pane panel-default" id="disclaimer">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                {!! \App\Helpers\Translation::translate('home.disclaimer.description.title') !!}
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
                <div class="panel tab-pane tab-pane panel-default" id="privacy">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                {!! \App\Helpers\Translation::translate('home.privacy.description.title', ['cooperation' => $cooperation->name]) !!}
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
@endpush
