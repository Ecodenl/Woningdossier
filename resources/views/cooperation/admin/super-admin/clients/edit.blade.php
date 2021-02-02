@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/super-admin/clients.index.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <form method="post" action="{{route('cooperation.admin.super-admin.clients.update', compact('client'))}}">
                        @csrf
                        @method('PUT')
                        @component('layouts.parts.components.form-group', ['input_name' => 'personal_access_tokens.name'])
                            <label for="">@lang('cooperation/admin/super-admin/clients.column-translations.name')</label>
                            <input type="text" name="clients[name]" value="{{old('clients.name', $client->name)}}" class="form-control">
                        @endcomponent

                        <button class="btn btn-primary">
                            @lang('cooperation/admin/super-admin/clients.edit.form.submit')
                        </button>
                    </form>
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
