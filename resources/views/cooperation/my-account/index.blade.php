@extends('cooperation.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('woningdossier.cooperation.my-account.index.header')</div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                @lang('woningdossier.cooperation.my-account.index.text')
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{route('cooperation.my-account.messages.index', ['cooperation' => $cooperation])}}" class="btn btn-primary">@lang('woningdossier.cooperation.my-account.index.messages')</a>
                                <a href="{{route('cooperation.my-account.settings.index', ['cooperation' => $cooperation])}}" class="btn btn-primary">@lang('woningdossier.cooperation.my-account.index.settings')</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
