@extends('cooperation.my-account.layouts.app')

@section('my_account_content')
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('woningdossier.cooperation.my-account.index.header')</div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                @lang('woningdossier.cooperation.my-account.index.text')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
