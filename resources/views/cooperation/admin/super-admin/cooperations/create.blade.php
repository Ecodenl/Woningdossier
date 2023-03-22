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
                        @csrf

                        {{-- TODO: Convert translations to cooperations file --}}
                        @component('layouts.parts.components.form-group', [
                            'input_name' => 'cooperations.name'
                        ])
                            <label for="">@lang('woningdossier.cooperation.admin.super-admin.cooperations.create.form.name')</label>
                            <input value="{{old('cooperations.name')}}" required type="text" class="form-control" name="cooperations[name]" placeholder="@lang('woningdossier.cooperation.admin.super-admin.cooperations.create.form.name')">
                        @endcomponent

                        @component('layouts.parts.components.form-group', [
                            'input_name' => 'cooperations.slug'
                        ])
                            <label for="">@lang('woningdossier.cooperation.admin.super-admin.cooperations.create.form.slug')</label>
                            <input value="{{old('cooperations.slug')}}" required type="text" class="form-control" name="cooperations[slug]" placeholder="@lang('woningdossier.cooperation.admin.super-admin.cooperations.create.form.slug')">
                        @endcomponent

                        @component('layouts.parts.components.form-group', [
                            'input_name' => 'cooperations.cooperation_email'
                        ])
                            <label for="">@lang('woningdossier.cooperation.admin.super-admin.cooperations.edit.form.cooperation_email')</label>
                            <input value="{{old('cooperations.cooperation_email')}}" type="text" class="form-control" name="cooperations[cooperation_email]" placeholder="@lang('woningdossier.cooperation.admin.super-admin.cooperations.create.form.cooperation_email')">
                        @endcomponent

                        @component('layouts.parts.components.form-group', [
                            'input_name' => 'cooperations.website_url'
                        ])
                            <label for="">@lang('woningdossier.cooperation.admin.super-admin.cooperations.edit.form.website_url')</label>
                            <input value="{{old('cooperations.website_url')}}" type="text" class="form-control" name="cooperations[website_url]" placeholder="@lang('woningdossier.cooperation.admin.super-admin.cooperations.create.form.website_url')">
                        @endcomponent

                        @component('layouts.parts.components.form-group', [
                            'input_name' => 'cooperation.econobis_wildcard'
                        ])
                            <label for="econobis-wildcard" class="control-label">
                                @lang('cooperation/admin/super-admin/cooperations.form.econobis-wildcard.label')
                            </label>
                            <input id="econobis-wildcard" type="text" class="form-control"
                                   placeholder="@lang('cooperation/admin/super-admin/cooperations.form.econobis-wildcard.placeholder')"
                                   name="cooperations[econobis_wildcard]"
                                   value="{{ old('cooperations.econobis_wildcard') }}">
                        @endcomponent

                        <button class="btn btn-success" type="submit">@lang('woningdossier.cooperation.admin.super-admin.cooperations.create.form.create')</button>
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
