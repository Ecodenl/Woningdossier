@extends('cooperation.layouts.app')
@push('css')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush
@section('content')
    <section class="section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="btn btn-primary">
                        test button
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="panel">
                        <div class="panel-body" >
                            <div id="sortable">
                                @foreach([1,2,3,4,5,6,7,8] as $number)
                                    <div class="form-builder panel panel-default" id="{{$number}}">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <label for="">maat</label>
                                                    <input type="text" class="form-control">
                                                    <h4>{{$number}}</h4>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('js')
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>

        $(".btn").click(function (e) {
            e.preventDefault();
            $("#sortable").append('<div class="form-builder panel panel-default"><div class="panel-body"><div class="row"><div class="col-sm-8"><label for="">maat</label><input type="text" class="form-control"></div></div></div>');
            $("#sortable").sortable('refresh');
        });


        $(document).ready(function () {

            var blocks = [];

            // get the id's off the blocks / panels
            $('.form-builder').each(function () {
                blocks.push($(this).attr('id'));
            });

            var master = $("#sortable");

            // make it sortable
            master.sortable({
                update: function () {

                    var order = [];

                    $(".form-builder").each(function () {
                        order.push($(this).attr('id'));
                    });

                    // create a new array with the order of the item and the navId
                    var newOrder = blocks.map(function (navOrder, navId) {
                        return navOrder, order[navId];
                    });

                    console.log(newOrder);
                }
            });

            master.disableSelection();
        });
    </script>
@endpush