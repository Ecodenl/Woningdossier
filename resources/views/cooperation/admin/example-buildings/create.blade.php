@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.example-buildings.create.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    @livewire('cooperation.admin.example-buildings.form', ['exampleBuilding' => null])
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $("form").on('submit', function (e) {
            var openTabId = $(".tab-content .tab-pane.active").attr('id');
            if (openTabId === 'new') {
                $("input[name='new']").val(1);
            } else {
                $("input[name='new']").val(0);
            }
        });
    </script>
@endpush