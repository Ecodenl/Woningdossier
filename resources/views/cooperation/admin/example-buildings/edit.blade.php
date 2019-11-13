@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3>Edit: {{ $exampleBuilding->name }}</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">

                    <form action="{{ route('cooperation.admin.example-buildings.update', ['id' => $exampleBuilding->id]) }}"
                          method="post">
                        {{ csrf_field() }}
                        {{ method_field("PUT") }}

                        @include('cooperation.admin.example-buildings.components.names')
                        @include('cooperation.admin.example-buildings.components.building-type')
                        @include('cooperation.admin.example-buildings.components.cooperation')
                        @include('cooperation.admin.example-buildings.components.order')
                        @include('cooperation.admin.example-buildings.components.is_default')
                        @include('cooperation.admin.example-buildings.components.contents')


                        <div class="form-group" style="margin-top: 5em;">
                            <input type="hidden" name="new" value="0">
                            <input type="submit" name="update" value="Update" class="btn btn-success btn-block">
                        </div>
                    </form>

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
