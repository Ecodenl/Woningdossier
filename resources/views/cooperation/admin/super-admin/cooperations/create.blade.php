@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.super-admin.cooperations.create.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{route('cooperation.admin.super-admin.cooperations.store')}}" method="post">
                        {{csrf_field()}}
                        <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="">@lang('woningdossier.cooperation.admin.super-admin.cooperations.create.form.name')</label>
                            <input value="{{old('name')}}" required type="text" class="form-control" name="name" placeholder="@lang('woningdossier.cooperation.admin.super-admin.cooperations.create.form.name')">

                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group {{ $errors->has('slug') ? ' has-error' : '' }}">
                            <label for="">@lang('woningdossier.cooperation.admin.super-admin.cooperations.create.form.slug')</label>
                            <input value="{{old('slug')}}" required type="text" class="form-control" name="slug" placeholder="@lang('woningdossier.cooperation.admin.super-admin.cooperations.create.form.slug')">

                            @if ($errors->has('slug'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('slug') }}</strong>
                                </span>
                            @endif
                        </div>

                        <button class="btn btn-success" type="submit">@lang('woningdossier.cooperation.admin.super-admin.cooperations.create.form.create')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>

        $('#table').DataTable({
            responsive: true,
            columnDefs: [
                {responsivePriority: 2, targets: 1},
                {responsivePriority: 1, targets: 0}
            ],
        });

    </script>
@endpush
