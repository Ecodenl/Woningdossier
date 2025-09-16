@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/cooperation/cooperation-admin/questionnaires.index.header'),
    'panelLink' => route('cooperation.admin.cooperation.questionnaires.create')
])

@section('content')
    <div class="w-full data-table">
        <table id="table" class="table fancy-table">
            <thead>
                <tr>
                    <th>@lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.index.table.columns.questionnaire-name')</th>
                    <th>@lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.index.table.columns.step')</th>
                    <th>@lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.index.table.columns.active')</th>
                    <th>@lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.index.table.columns.actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($questionnaires as $questionnaire)
                    <tr>
                        <td>{{$questionnaire->name}}</td>
                        <td>{{ $questionnaire->steps()->pluck('name')->implode(', ') }}</td>
                        <td>
                            <div class="checkbox-wrapper w-40 mb-0">
                                <input type="checkbox" id="active-{{$questionnaire->id}}" value="1"
                                       data-questionnaire-id="{{$questionnaire->id}}"
                                       @if($questionnaire->isActive()) checked @endif class="active-questionnaire">
                                <label for="active-{{$questionnaire->id}}">
                                    <span class="checkmark"></span>
                                    <span class="text">{{ $questionnaire->isActive() ? 'Actief' : 'Niet actief' }}</span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <a class="btn btn-blue mb-2 mr-2"
                               href="{{route('cooperation.admin.cooperation.questionnaires.edit', compact('questionnaire'))}}">
                                @lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.index.table.columns.edit')
                            </a>

                            @can('delete', $questionnaire)
                                <button data-questionnaire-id="{{$questionnaire->id}}" type="button"
                                        class="destroy btn btn-red">
                                    @lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.index.table.columns.destroy')
                                </button>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('js')
    <script type="module" nonce="{{ $cspNonce }}">
        const destroyQuestionnaireRoute = '{{route('cooperation.admin.cooperation.questionnaires.destroy', ['questionnaire' => ':questionnaire-id'])}}';

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

        document.on('click', '.destroy', function (event) {
            if (this.classList.contains('destroy') && confirm('@lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.destroy.are-you-sure')')) {
                fetch(destroyQuestionnaireRoute.replace(':questionnaire-id', $(this).data('questionnaire-id')), {
                    method: "DELETE",
                    headers: {
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                }).then((response) => location.reload());
            } else {
                event.preventDefault();
                return false;
            }
        });

        document.on('change', '.active-questionnaire', function (event) {
            this.closest('.checkbox-wrapper').querySelector('.text').textContent = this.checked ? 'Actief' : 'Niet actief';

            fetch('{{route('cooperation.admin.cooperation.questionnaires.set-active')}}', {
                method: "POST",
                headers: {
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    questionnaire_active: this.checked,
                    questionnaire_id: this.dataset.questionnaireId,
                }),
            });
        });
    </script>
@endpush

