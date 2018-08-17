@extends('cooperation.admin.cooperation.coordinator.layouts.app')

@section('coordinator_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.index.header')

        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">

                </div>
            </div>
        </div>
    </div>
@endsection



@push('css')
    <link rel="stylesheet" href="{{asset('css/select2/select2.min.css')}}">
    @push('js')
        <script src="{{asset('js/select2.js')}}"></script>

        <script>

            $(document).ready(function () {

                $(".coach").select2({
                    placeholder: "@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.index.form.select-coach')",
                    maximumSelectionLength: Infinity
                });
            });
        </script>
    @endpush
