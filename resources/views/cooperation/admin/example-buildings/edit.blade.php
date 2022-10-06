@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3>@lang('cooperation/admin/example-buildings.edit.title', ['name' => $exampleBuilding->name])</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    @livewire('cooperation.admin.example-buildings.form', compact('exampleBuilding'))
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
