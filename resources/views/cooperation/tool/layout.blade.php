@extends('cooperation.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                @include('cooperation.tool.progress')
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">@yield('step_title', '')</div>

                    <div class="panel-body">
                        @yield('step_content', '')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


