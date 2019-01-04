@extends('cooperation.my-account.layouts.app')

@section('my_account_content')
    <div class="container">
        <div class="row">
            <div class="col-md-10">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('woningdossier.cooperation.import-center.title')</div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                @lang('woningdossier.cooperation.my-account.import-center.text')
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">

                                @component('cooperation.tool.components.alert', ['alertType' => 'success'])
                                    pils!
                                @endcomponent
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
