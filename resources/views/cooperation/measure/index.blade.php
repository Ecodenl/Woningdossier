@extends('cooperation.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('woningdossier.cooperation.help.title')</div>

                    <div class="panel-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">@lang('woningdossier.cooperation.help.help.title')</div>
                                    <div class="panel-body">
                                        <ol>
                                            @foreach($files as $file)
                                                <li><a download="" href="{{asset($file)}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset($file)))))}}</a></li>
                                            @endforeach
                                        </ol>
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
