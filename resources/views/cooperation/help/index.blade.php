@extends('cooperation.layouts.app')

@section('page_class', 'page-help')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('woningdossier.cooperation.help.title')</div>

                    <div class="panel-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <p>@lang('woningdossier.cooperation.help.help.description')</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <ul>
                                    @foreach($files as $categoryImage => $categoryFiles)
                                        @foreach($categoryFiles as $file)
                                            <li><img src="{{ asset('images/' . $categoryImage . '.png') }}"class="img-circle"/> <a download="" href="{{ asset($file) }}">{{ ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset($file))))) }}</a></li>
                                        @endforeach
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
