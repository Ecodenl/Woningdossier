@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('cooperation.admin.layouts.sidebar-menu')
            <div class="col-md-9">
                @yield('super_admin_content')
            </div>
        </div>
    </div>
@endsection

@prepend('css')
    <link rel="stylesheet" href="{{asset('css/select2/select2.min.css')}}">
    @vite('resources/sass/admin/datatables/datatables.scss')
@endprepend

@prepend('js')
    @vite('resources/js/datatables/datatables.js')
    @vite('resources/js/select2.js')

    <script>
        $(document).ready(function () {

            $('.collapse').on('shown.bs.collapse', function(){
                $(this).parent().find(".glyphicon-chevron-down").removeClass("glyphicon-chevron-down").addClass("glyphicon-chevron-up");
            }).on('hidden.bs.collapse', function(){
                $(this).parent().find(".glyphicon-chevron-up").removeClass("glyphicon-chevron-up").addClass("glyphicon-chevron-down");
            });
        });

        $.extend( true, $.fn.dataTable.defaults, {
            language: {
                url: "{{asset('js/datatables-dutch.json')}}"
            }
        });
    </script>
@endprepend
