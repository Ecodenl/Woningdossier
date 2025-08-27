@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/translations.index.header')
])

@section('content')
    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
            <thead>
                <tr>
                    <th>@lang('cooperation/admin/super-admin/translations.index.table.columns.name')</th>
                    <th>@lang('cooperation/admin/super-admin/translations.index.table.columns.actions')</th>
                </tr>
            </thead>
            <tbody>
                {{--todo: this needs refactoring so we can just treat translations as translations instead of steps--}}
                @foreach($mailLangFiles as $group =>  $translation)
                    <tr>
                        <td>{{$translation}}</td>
                        <td>
                            <a class="btn btn-blue"
                               href="{{route('cooperation.admin.super-admin.translations.edit', ['group' => str_replace('/', '_', $group)])}}">
                                @lang('cooperation/admin/super-admin/translations.index.table.see')
                            </a>
                        </td>
                    </tr>
                @endforeach
                @foreach($steps as $step)
                    <tr>
                        <td>{{$step->name}}</td>
                        <td>
                            <a class="btn btn-blue"
                               href="{{route('cooperation.admin.super-admin.translations.edit', ['group' => $step->short])}}">
                                @lang('cooperation/admin/super-admin/translations.index.table.see')
                            </a>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td>@lang('cooperation/admin/super-admin/translations.index.table.main-translations')</td>
                    <td>
                        <a class="btn btn-blue"
                           href="{{route('cooperation.admin.super-admin.translations.edit', ['group' => 'general'])}}">
                            @lang('cooperation/admin/super-admin/translations.index.table.see')
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>@lang('cooperation/admin/super-admin/translations.index.table.pdf')</td>
                    <td>
                        <a class="btn btn-blue"
                           href="{{route('cooperation.admin.super-admin.translations.edit', ['group' => 'pdf-user-report'])}}">
                            @lang('cooperation/admin/super-admin/translations.index.table.see')
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>My plan</td>
                    <td>
                        <a class="btn btn-blue"
                           href="{{route('cooperation.admin.super-admin.translations.edit', ['group' => 'my-plan'])}}">
                            @lang('cooperation/admin/super-admin/translations.index.table.see')
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>Home</td>
                    <td>
                        <a class="btn btn-blue"
                           href="{{route('cooperation.admin.super-admin.translations.edit', ['group' => 'home'])}}">
                            @lang('cooperation/admin/super-admin/translations.index.table.see')
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>Help</td>
                    <td>
                        <a class="btn btn-blue"
                           href="{{route('cooperation.admin.super-admin.translations.edit', ['group' => 'cooperation_frontend_help'])}}">
                            @lang('cooperation/admin/super-admin/translations.index.table.see')
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection

@push('js')
    <script type="module" nonce="{{ $cspNonce }}">
        document.addEventListener('DOMContentLoaded', function () {
            new DataTable('#table', {
                scrollX: true,
                responsive: false,
                language: {
                    url: '{{ asset('js/datatables-dutch.json') }}'
                },
                layout: {
                    bottomEnd: {
                        paging: {
                            firstLast: false
                        }
                    }
                },
            });
        });
    </script>
@endpush