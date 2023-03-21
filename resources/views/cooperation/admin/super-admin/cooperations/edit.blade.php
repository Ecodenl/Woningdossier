@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.super-admin.cooperations.edit.header', ['name' => $cooperationToEdit->name])
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{route('cooperation.admin.super-admin.cooperations.update')}}" method="post">
                        {{csrf_field()}}
                        <input type="hidden" name="cooperation_id" value="{{$cooperationToEdit->id}}">
                        <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="">@lang('woningdossier.cooperation.admin.super-admin.cooperations.edit.form.name')</label>
                            <input value="{{old('name', $cooperationToEdit->name)}}" required type="text" class="form-control" name="name" placeholder="@lang('woningdossier.cooperation.admin.super-admin.cooperations.edit.form.name')">

                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group {{ $errors->has('slug') ? ' has-error' : '' }}">
                            <label for="">@lang('woningdossier.cooperation.admin.super-admin.cooperations.edit.form.slug')</label>
                            <input value="{{old('slug', $cooperationToEdit->slug)}}" required type="text" class="form-control" name="slug" placeholder="@lang('woningdossier.cooperation.admin.super-admin.cooperations.edit.form.slug')">

                            @if ($errors->has('slug'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('slug') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group {{ $errors->has('cooperation_email') ? ' has-error' : '' }}">
                            <label for="">@lang('woningdossier.cooperation.admin.super-admin.cooperations.edit.form.cooperation_email')</label>
                            <input value="{{old('cooperation_email', $cooperationToEdit->cooperation_email)}}" type="text" class="form-control" name="cooperation_email" placeholder="@lang('woningdossier.cooperation.admin.super-admin.cooperations.edit.form.cooperation_email')">

                            @if ($errors->has('cooperation_email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('cooperation_email') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group {{ $errors->has('website_url') ? ' has-error' : '' }}">
                            <label for="">@lang('woningdossier.cooperation.admin.super-admin.cooperations.edit.form.website_url')</label>
                            <input value="{{old('website_url', $cooperationToEdit->website_url)}}"  type="text" class="form-control" name="website_url" placeholder="@lang('woningdossier.cooperation.admin.super-admin.cooperations.edit.form.website_url')">

                            @if ($errors->has('website_url'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('website_url') }}</strong>
                                </span>
                            @endif
                        </div>

                        @php
                            // Changed in other branch, so backwards compatible until merged
                            $cooperationToUpdate ??= $cooperationToEdit;
                            $transKey = empty($cooperationToUpdate->econobis_api_key) ? 'label' : 'label-replace';
                        @endphp
                        @component('layouts.parts.components.form-group', [
                            'input_name' => 'cooperation.econobis_api_key'
                        ])
                            <label for="econobis-api-key" class="control-label">
                                @lang("cooperation/admin/super-admin/cooperations.form.econobis-api-key.{$transKey}")
                            </label>
                            <input id="econobis-api-key" type="text" class="form-control"
                                   placeholder="@lang('cooperation/admin/super-admin/cooperations.form.econobis-api-key.placeholder')"
                                   name="cooperations[econobis_api_key]"
                                   value="{{ old('cooperations.econobis_api_key') }}">
                        @endcomponent

                        <button class="btn btn-success" type="submit">@lang('woningdossier.cooperation.admin.super-admin.cooperations.edit.form.update')</button>
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
        }):

    </script>
@endpush
