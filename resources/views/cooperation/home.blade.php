@extends('cooperation.layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">@lang('woningdossier.cooperation.home.disclaimer.panel-title')</div>

                <div class="panel-body">

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-sm-12">
                            <a href="{{route('cooperation.conversation-requests.index', ['cooperation' => $cooperation, 'action' => \App\Models\PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION])}}" class="btn btn-primary">@lang('woningdossier.cooperation.help.help.help-with-filling-tool')</a>
                            <a href="{{route('cooperation.tool.general-data.index', ['cooperation' => $cooperation])}}" class="btn btn-primary">@lang('woningdossier.cooperation.help.help.no-help-with-filling-tool')</a>
                        </div>
                    </div>
                    <br>
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
@endsection
