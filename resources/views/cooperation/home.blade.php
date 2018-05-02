@extends('cooperation.layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                        <br><br>


                    <a href="{{ route('cooperation.tool.index', ['cooperation' => $cooperation]) }}">@lang('woningdossier.cooperation.tool.title')</a>
                        <br><br>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">@lang('woningdossier.cooperation.home.disclaimer.title')</div>
                                    <div class="panel-body">
                                        @lang('woningdossier.cooperation.home.disclaimer.description')
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
