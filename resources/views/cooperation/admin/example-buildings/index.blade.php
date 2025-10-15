@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('woningdossier.cooperation.admin.example-buildings.index.header'),
    'panelLink' => route('cooperation.admin.example-buildings.create')
])

@section('content')
    @if(Hoomdossier::user()->hasRoleAndIsCurrentRole('super-admin'))
        <livewire:cooperation.admin.example-buildings.csv-export :cooperation="$cooperation"/>
    @endif

    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
            <thead>
                <tr>
                    <th>@lang('cooperation/admin/example-buildings.index.table.name')</th>
                    <th>@lang('cooperation/admin/example-buildings.index.table.order')</th>
                    <th>@lang('cooperation/admin/example-buildings.index.table.cooperation')</th>
                    <th>@lang('cooperation/admin/example-buildings.index.table.default')</th>
                    <th>@lang('cooperation/admin/example-buildings.index.table.actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($exampleBuildings as $exampleBuilding)
                    <tr>
                        <td>{{ $exampleBuilding->name }}</td>
                        <td>{{ $exampleBuilding->order }}</td>
                        <td>
                            @if($exampleBuilding->cooperation instanceof \App\Models\Cooperation)
                                {{ $exampleBuilding->cooperation->name }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($exampleBuilding->is_default)
                                <i class="icon-md icon-check-circle-green"></i>
                            @endif
                        </td>
                        <td>
                            <a title="Kopiëren"
                               href="{{ route('cooperation.admin.example-buildings.copy', compact('exampleBuilding')) }}"
                               class="btn btn-outline-yellow inline-flex items-center">
                                <i class="icon-md icon-document"></i>
                            </a>
                            <a title="Bewerken"
                               href="{{ route('cooperation.admin.example-buildings.edit', compact('exampleBuilding')) }}"
                               class="btn btn-outline-blue inline-flex items-center">
                                <i class="icon-md icon-tools"></i>
                            </a>
                            <form style="display:inline;" method="POST"
                                  action="{{ route('cooperation.admin.example-buildings.destroy', compact('exampleBuilding')) }}">
                                @csrf
                                @method('DELETE')
                                <button title="@lang('default.buttons.destroy')"
                                        type="submit" class="btn btn-outline-red inline-flex items-center destroy-example-building">
                                    <i class="icon-md icon-trash-can-red"></i>
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
    <script type="module" nonce="{{ $cspNonce }}">
        document.addEventListener('DOMContentLoaded', function () {
            new DataTable('#table', {
                autoWidth: false,
                pageLength: 50,
                // columnDefs: [
                //     {responsivePriority: 2, targets: 1},
                //     {responsivePriority: 1, targets: 0}
                // ],
                columnDefs: [
                    {width: '25%', targets: 0},
                    {width: '15%', targets: 1},
                    {width: '20%', targets: 2},
                    {width: '15%', targets: 3},
                    {width: '25%', targets: 4},
                ],
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

        document.on('click', '.destroy-example-building', function (event) {
            if (! this.classList.contains('destroy-example-building') || ! confirm('@lang('cooperation/admin/example-buildings.destroy.confirm')')) {
                event.preventDefault();
                return false;
            }
        });
    </script>
@endpush