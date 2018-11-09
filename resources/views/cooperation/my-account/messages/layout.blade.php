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

                    <div id="@yield('panel_body_id', '')" class="panel-body @yield('panel_body_class', '')">
                        @yield('messages_content')
                    </div>
                    <div class="panel-footer">
                        @yield('messages_footer')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection