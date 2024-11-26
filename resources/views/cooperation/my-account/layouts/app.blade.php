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

    <script src="{{asset('js/hoomdossier.js')}}"></script>

    <script type="module">
        document.addEventListener('DOMContentLoaded', function () {

            pollForMessageCount();
            $('.collapse').on('shown.bs.collapse', function(){
                $(this).parent().find(".glyphicon-chevron-down").removeClass("glyphicon-chevron-down").addClass("glyphicon-chevron-up");
            }).on('hidden.bs.collapse', function(){
                $(this).parent().find(".glyphicon-chevron-up").removeClass("glyphicon-chevron-up").addClass("glyphicon-chevron-down");
            });
        });
    </script>
@endpush
