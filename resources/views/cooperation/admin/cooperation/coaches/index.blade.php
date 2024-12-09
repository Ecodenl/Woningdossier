@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('woningdossier.cooperation.admin.cooperation.coaches.index.header')
])

@section('content')
    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
            <thead>
                <tr>
                    <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.index.table.columns.name')</th>
                    <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.index.table.columns.street-house-number')</th>
                    <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.index.table.columns.zip-code')</th>
                    <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.index.table.columns.city')</th>
                    <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.index.table.columns.email')</th>
                    <th>@lang('woningdossier.cooperation.admin.cooperation.coaches.index.table.columns.roles')</th>
                </tr>
            </thead>
            <tbody>
                @php /** @var \App\Models\User $user */ @endphp
                @foreach($users as $user)
                    @php $building = $user->building; @endphp
                    @if($building instanceof \App\Models\Building)
                        <tr>
                            <td>{{$user->getFullName()}}</td>
                            <td>
                                <a class="in-text"
                                   href="{{route('cooperation.admin.cooperation.coaches.show', compact('user'))}}">
                                    {{$building->street}} {{$building->number}} {{$building->extension}}
                                </a>
                            </td>
                            <td>{{$building->postal_code}}</td>
                            <td>
                                {{$building->city}}
                            </td>
                            <td>
                                {{$user->account->email}}
                            </td>
                            <td>
                                {{$user->roles->pluck('human_readable_name')->implode(', ')}}
                            </td>
                        </tr>
                    @endif
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