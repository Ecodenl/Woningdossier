@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/cooperation-presets.show.title'),
    'panelLink' => route('cooperation.admin.super-admin.cooperation-presets.cooperation-preset-contents.create', compact('cooperationPreset'))
])

@section('content')
    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
            <thead>
                <tr>
                    <th>@lang('cooperation/admin/super-admin/cooperation-presets.show.table.columns.title')</th>
                    <th>@lang('cooperation/admin/super-admin/cooperation-presets.show.table.columns.actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cooperationPreset->cooperationPresetContents as $cooperationPresetContent)
                    <tr>
                        @php
                            // We could do _another_ mapping, or we can just attempt a few columns, as we
                            // mostly use the same names anyway
                            $title = $cooperationPresetContent->content['title'] ?? $cooperationPresetContent->content['name'] ?? null;

                            // Might be translatable
                            if (is_array($title)) {
                                $title = $title['nl'] ?? null;
                            }
                        @endphp

                        <td>
                            {{ $title }}
                        </td>
                        <td>
                            <a href="{{ route('cooperation.admin.super-admin.cooperation-presets.cooperation-preset-contents.edit', compact('cooperationPreset', 'cooperationPresetContent')) }}"
                               class="btn btn-blue">
                                @lang('cooperation/admin/super-admin/cooperation-preset-contents.edit.title')
                            </a>
                            <form action="{{ route('cooperation.admin.super-admin.cooperation-presets.cooperation-preset-contents.destroy', compact('cooperationPreset', 'cooperationPresetContent')) }}"
                                  method="POST" class="pl-2 inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-red" type="submit">
                                    @lang('default.buttons.destroy')
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('js')
    <script type="module">
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

        document.on('click', 'button[type="submit"]', (event) => {
            if (! confirm('@lang('cooperation/admin/super-admin/cooperation-preset-contents.destroy.confirm')')) {
                event.preventDefault();
                return false;
            }
        });
    </script>
@endpush

