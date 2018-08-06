@extends('cooperation.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('cooperation.my-account.messages.side-nav')
            <div class="col-md-10">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        @yield('messages_header', '')

                    </div>

                    <div class="panel-body">
                        @yield('messages_content')

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection