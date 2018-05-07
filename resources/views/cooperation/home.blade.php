@extends('cooperation.layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">@lang('woningdossier.cooperation.tool.title')</div>

                <div class="panel-body">

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-sm-12">
                            <p>@lang('woningdossier.cooperation.home.disclaimer.description')</p>
                        </div>
                    </div>
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
