@extends('cooperation.layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @yield('my_account_content')
            </div>
        </div>
    </div>
@endsection


@push('js')

    <script src="{{ asset('js/disable-auto-fill.js') }}"></script>
    <script src="{{asset('js/hoomdossier.js')}}"></script>

    <script>
        $(document).ready(function () {

            pollForMessageCount();
            $('.collapse').on('shown.bs.collapse', function(){
                $(this).parent().find(".glyphicon-chevron-down").removeClass("glyphicon-chevron-down").addClass("glyphicon-chevron-up");
            }).on('hidden.bs.collapse', function(){
                $(this).parent().find(".glyphicon-chevron-up").removeClass("glyphicon-chevron-up").addClass("glyphicon-chevron-down");
            });
        });
    </script>
@endpush
