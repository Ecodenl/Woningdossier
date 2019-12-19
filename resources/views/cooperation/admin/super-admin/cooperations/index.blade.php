@extends('cooperation.admin.layouts.app')

@section('content')

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.super-admin.cooperations.index.header')
            <a href="{{route('cooperation.admin.super-admin.cooperations.create')}}" class=" btn-sm btn btn-primary pull-right">@lang('woningdossier.cooperation.admin.super-admin.cooperations.index.create')</a>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-responsive table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.cooperations.index.table.columns.name')</th>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.cooperations.index.table.columns.slug')</th>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.cooperations.index.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($cooperations as $cooperation)
                                <tr>
                                    <td>{{$cooperation->name}}</td>
                                    <td>{{$cooperation->slug}}</td>
                                    <td>
                                        <a href="{{route('cooperation.admin.super-admin.cooperations.edit', ['cooperation' => $currentCooperation, 'cooperationToEdit' => $cooperation])}}" class="btn btn-default">@lang('woningdossier.cooperation.admin.super-admin.cooperations.index.edit')</a>
                                        <a href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index', ['cooperation' => $currentCooperation, 'cooperationToManage' => $cooperation])}}" class="btn btn-default">@lang('woningdossier.cooperation.admin.super-admin.cooperations.index.show')</a>
                                        @if($cooperation->slug != 'hoom')
                                            <a data-toggle="modal" data-target="{{"#destroy-cooperation-{$cooperation->id}" }}" class="btn btn-danger">
                                                @lang('woningdossier.cooperation.admin.super-admin.cooperations.index.destroy')
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @component('cooperation.tool.components.modal', ['id' => "destroy-cooperation-{$cooperation->id}", 'title' => "Verwijder {$cooperation->name}"])
                                    <form action="{{route('cooperation.admin.super-admin.cooperations.destroy', ['cooperationToDestroy' => $cooperation])}}" method="POST">
                                        {{csrf_field()}}
                                        {{method_field('delete')}}
                                        <p class="text-danger">
                                            @lang('woningdossier.cooperation.admin.super-admin.cooperations.index.modal.text', ['cooperation' => $cooperation->name])
                                        </p>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">
                                            @lang('woningdossier.cooperation.admin.super-admin.cooperations.index.modal.cancel')
                                        </button>

                                        <button type="submit" class="btn btn-danger pull-right">
                                            @lang('woningdossier.cooperation.admin.super-admin.cooperations.index.modal.destroy')
                                        </button>
                                    </form>
                                @endcomponent
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        $(document).ready(function () {
            $('#table').DataTable({
                responsive: true,
                columnDefs: [
                    {responsivePriority: 2, targets: 1},
                    {responsivePriority: 1, targets: 0}
                ],
            });
        });

    </script>
@endpush
